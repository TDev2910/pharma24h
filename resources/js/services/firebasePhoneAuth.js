import { auth } from '../config/firebase.js';
import { 
    signInWithPhoneNumber, 
    RecaptchaVerifier,
    PhoneAuthProvider,
    signInWithCredential
} 
from 'firebase/auth';

class FirebasePhoneAuthService 
{
    constructor() 
    {
        this.recaptchaVerifier = null;
        this.confirmationResult = null;
        this.rateLimitKey = 'firebase_otp_attempts';
        this.maxAttempts = 3;
        this.cooldownTime = 60 * 1000; // 1 phút
        this.otpExpiryTime = 5 * 60 * 1000; // 5 phút
    }

    /**
     * Khởi tạo reCAPTCHA verifier
     * @param {string} containerId - ID của element chứa reCAPTCHA
     */
    initRecaptcha(containerId) {
        this.recaptchaVerifier = new RecaptchaVerifier(auth, containerId, {
            'size': 'invisible',
            'callback': (response) => {
                console.log('reCAPTCHA solved');
            },
            'expired-callback': () => {
                console.log('reCAPTCHA expired');
            }
        });

        // ✅ THÊM: Xử lý lỗi reCAPTCHA Enterprise với fallback
        this.recaptchaVerifier.render().then((widgetId) => {
            console.log('reCAPTCHA widget đã render, ID:', widgetId);
        }).catch(error => {
            console.warn('reCAPTCHA Enterprise failed, falling back to v2:', error);
            // Fallback to v2
            this.recaptchaVerifier = new RecaptchaVerifier(auth, containerId, {
                'size': 'invisible'
            });
            return this.recaptchaVerifier.render();
        });
    }

    /**
     * Kiểm tra rate limiting
     */
    checkRateLimit() {
        const attempts = JSON.parse(localStorage.getItem(this.rateLimitKey) || '[]');
        const now = Date.now();
        const recentAttempts = attempts.filter(time => now - time < this.cooldownTime);
        
        if (recentAttempts.length >= this.maxAttempts) {
            const remainingTime = Math.ceil((recentAttempts[0] + this.cooldownTime - now) / 1000);
            throw new Error(`Quá nhiều yêu cầu. Vui lòng đợi ${remainingTime} giây.`);
        }
        
        attempts.push(now);
        localStorage.setItem(this.rateLimitKey, JSON.stringify(attempts));
    }

    /**
     * Lưu thời gian gửi OTP
     */
    saveOTPTime() {
        localStorage.setItem('otp_sent_time', Date.now().toString());
    }

    /**
     * Kiểm tra OTP có hết hạn không
     */
    isOTPExpired() {
        const sentTime = parseInt(localStorage.getItem('otp_sent_time') || '0');
        const now = Date.now();
        return (now - sentTime) > this.otpExpiryTime;
    }

    /**
     * Gửi mã OTP đến số điện thoại
     * @param {string} phoneNumber - Số điện thoại (format: +84xxxxxxxxx)
     * @returns {Promise} - Promise với confirmation result
     */
    async sendOTP(phoneNumber) {
        try {
            // Kiểm tra rate limiting
            this.checkRateLimit();
            
            // Đảm bảo phone number có format đúng
            const formattedPhone = this.formatPhoneNumber(phoneNumber);
            
            console.log('Sending OTP to:', formattedPhone); // ✅ Debug log
            console.log('reCAPTCHA verifier status:', this.recaptchaVerifier ? 'Ready' : 'Not ready'); // ✅ Debug log
            
            // ✅ THÊM: Retry logic cho Firebase 503 errors
            let retryCount = 0;
            const maxRetries = 3;
            let lastError;
            
            while (retryCount < maxRetries) {
                try {
                    this.confirmationResult = await signInWithPhoneNumber(
                        auth, 
                        formattedPhone, 
                        this.recaptchaVerifier
                    );
                    break; // Thành công, thoát vòng lặp
                } catch (error) {
                    retryCount++;
                    lastError = error;
                    console.log(`Attempt ${retryCount} failed:`, error.code);
                    
                    // Chỉ retry với lỗi 503 hoặc network errors
                    if (error.code === 'auth/network-request-failed' || 
                        error.code === 'auth/error-code:-39' ||
                        error.message.includes('503')) {
                        
                        if (retryCount >= maxRetries) {
                            throw error; // Throw lỗi cuối cùng
                        }
                        
                        // Đợi 2-5 giây trước khi retry (exponential backoff)
                        const waitTime = Math.min(2000 * Math.pow(2, retryCount - 1), 10000);
                        console.log(`Retrying in ${waitTime}ms...`);
                        await new Promise(resolve => setTimeout(resolve, waitTime));
                    } else {
                        // Lỗi khác không retry
                        throw error;
                    }
                }
            }
            
            // ✅ THÊM: Kiểm tra confirmationResult sau khi retry
            if (!this.confirmationResult) {
                throw new Error('Không thể kết nối đến Firebase sau nhiều lần thử');
            }
            
            // Lưu thời gian gửi OTP
            this.saveOTPTime();
            
            return {
                success: true,
                message: 'Mã OTP đã được gửi đến số điện thoại của bạn. Mã có hiệu lực trong 5 phút.',
                expiryTime: this.otpExpiryTime
            };
        } catch (error) {
            console.error('Error sending OTP:', error);
            
            // ✅ THÊM: Xử lý lỗi cụ thể
            let errorMessage = 'Có lỗi xảy ra khi gửi OTP';
            
            if (error.code === 'auth/error-code:-39' || error.message.includes('503')) {
                errorMessage = 'Firebase service tạm thời không khả dụng. Vui lòng thử lại sau ít phút.';
            } else if (error.code === 'auth/too-many-requests') {
                errorMessage = 'Quá nhiều yêu cầu. Vui lòng đợi trước khi thử lại.';
            } else if (error.code === 'auth/invalid-phone-number') {
                errorMessage = 'Số điện thoại không hợp lệ.';
            } else if (error.code === 'auth/quota-exceeded') {
                errorMessage = 'Đã vượt quá hạn mức gửi SMS. Vui lòng thử lại sau.';
            }
            
            return {
                success: false,
                message: errorMessage,
                error: error
            };
        }
    }

    /**
     * Xác thực mã OTP
     * @param {string} otpCode - Mã OTP người dùng nhập
     * @returns {Promise} - Promise với kết quả xác thực
     */
    async verifyOTP(otpCode) {
        try {
            // ✅ THÊM: Kiểm tra confirmationResult tồn tại
            if (!this.confirmationResult || !this.confirmationResult.verificationId) {
                return {
                    success: false,
                    message: 'Phiên xác thực đã hết hạn. Vui lòng gửi lại mã OTP!',
                    expired: true
                };
            }

            // Kiểm tra OTP có hết hạn không
            if (this.isOTPExpired()) {
                return {
                    success: false,
                    message: 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.',
                    expired: true
                };
            }

            const credential = PhoneAuthProvider.credential(
                this.confirmationResult.verificationId,
                otpCode
            );
            
            const result = await signInWithCredential(auth, credential);
            
            // Xóa thời gian OTP sau khi xác thực thành công
            localStorage.removeItem('otp_sent_time');
            
            return {
                success: true,
                user: result.user,
                message: 'Xác thực thành công'
            };
        } catch (error) {
            console.error('Error verifying OTP:', error);
            return {
                success: false,
                message: this.getErrorMessage(error.code),
                error: error
            };
        }
    }

    /**
     * Format số điện thoại Việt Nam 
     * @param {string} phoneNumber - Số điện thoại
     * @returns {string} - Số điện thoại đã format
     */
    formatPhoneNumber(phoneNumber) {
        // Loại bỏ tất cả ký tự không phải số
        const cleaned = phoneNumber.replace(/\D/g, '');

        // Luôn lấy 9 số cuối cùng của chuỗi
        // Ví dụ: "0901645269" -> "901645269"
        // Ví dụ: "840901645269" -> "901645269"
        const last9Digits = cleaned.slice(-9);

        // ✅ THÊM: Validation phone number
        if (last9Digits.length !== 9) {
            throw new Error('Số điện thoại không hợp lệ. Vui lòng nhập đúng 10 số.');
        }

        // Kiểm tra số điện thoại Việt Nam hợp lệ
        const firstDigit = last9Digits[0];
        if (!['3', '5', '7', '8', '9'].includes(firstDigit)) {
            throw new Error('Số điện thoại không hợp lệ. Số Việt Nam phải bắt đầu bằng 3, 5, 7, 8, hoặc 9.');
        }

        // Trả về định dạng chuẩn +84
        return '+84' + last9Digits;
    }

    /**
     * Lấy thông báo lỗi 
     * @param {string} errorCode - Mã lỗi Firebase
     * @returns {string} - Thông báo lỗi
     */
    getErrorMessage(errorCode) {
        const errorMessages = {
            'auth/invalid-phone-number': 'Số điện thoại không hợp lệ',
            'auth/too-many-requests': 'Quá nhiều yêu cầu. Vui lòng thử lại sau',
            'auth/invalid-verification-code': 'Mã OTP không đúng',
            'auth/code-expired': 'Mã OTP đã hết hạn',
            'auth/credential-already-in-use': 'Số điện thoại đã được sử dụng',
            'auth/phone-number-already-exists': 'Số điện thoại đã tồn tại',
            'auth/operation-not-allowed': 'Thao tác không được phép',
            'auth/network-request-failed': 'Lỗi kết nối mạng'
        };
        
        return errorMessages[errorCode] || 'Có lỗi xảy ra, vui lòng thử lại';
    }

    /**
     * Dọn dẹp reCAPTCHA
     */
    cleanup() {
        if (this.recaptchaVerifier) {
            this.recaptchaVerifier.clear();
            this.recaptchaVerifier = null;
        }
    }
}

// Export singleton instance
export default new FirebasePhoneAuthService();
