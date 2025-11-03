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
        this.otpExpiryTime = 2 * 60 * 1000; // 2 phút (Firebase thực tế chỉ cho phép 1-2 phút)
        
        // Khôi phục confirmationResult từ localStorage nếu có
        this.restoreConfirmationResult();
    }

    /**
     * Khôi phục confirmationResult từ localStorage
     */
    restoreConfirmationResult() {
        try {
            const savedVerificationId = localStorage.getItem('firebase_verification_id');
            const savedPhone = localStorage.getItem('firebase_phone');
            
            if (savedVerificationId && savedPhone) {
                // Tạo lại confirmationResult object từ verificationId đã lưu
                this.confirmationResult = {
                    verificationId: savedVerificationId,
                    _phoneNumber: savedPhone
                };
                
                // ConfirmationResult restored from localStorage
            }
        } catch (error) {
            console.error('Error restoring confirmationResult:', error);
        }
    }

    /**
     * Lưu confirmationResult vào localStorage
     */
    saveConfirmationResult(verificationId, phoneNumber) {
        try {
            localStorage.setItem('firebase_verification_id', verificationId);
            localStorage.setItem('firebase_phone', phoneNumber);
        } catch (error) {
            console.error('Error saving confirmationResult:', error);
        }
    }

    /**
     * Xóa confirmationResult khỏi localStorage
     */
    clearConfirmationResult() {
        localStorage.removeItem('firebase_verification_id');
        localStorage.removeItem('firebase_phone');
        this.confirmationResult = null;
    }

    /**
     * Khởi tạo reCAPTCHA verifier
     * @param {string} containerId - ID của element chứa reCAPTCHA
     */
    initRecaptcha(containerId) {
        this.recaptchaVerifier = new RecaptchaVerifier(auth, containerId, {
            'size': 'invisible',
            'callback': (response) => {
                // reCAPTCHA solved
            },
            'expired-callback': () => {
                // reCAPTCHA expired
            }
        });

        // ✅ THÊM: Xử lý lỗi reCAPTCHA Enterprise với fallback
        this.recaptchaVerifier.render().catch(error => {
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
                    
                    // Chỉ retry với lỗi 503 hoặc network errors
                    if (error.code === 'auth/network-request-failed' || 
                        error.code === 'auth/error-code:-39' ||
                        error.message.includes('503')) {
                        
                        if (retryCount >= maxRetries) {
                            throw error; // Throw lỗi cuối cùng
                        }
                        
                        // Đợi 2-5 giây trước khi retry (exponential backoff)
                        const waitTime = Math.min(2000 * Math.pow(2, retryCount - 1), 10000);
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
            
            // ✅ THÊM: Lưu verificationId vào localStorage để khôi phục sau khi reload
            if (this.confirmationResult && this.confirmationResult.verificationId) {
                this.saveConfirmationResult(
                    this.confirmationResult.verificationId,
                    formattedPhone
                );
            }
            
            // Lưu thời gian gửi OTP
            this.saveOTPTime();
            
            return {
                success: true,
                message: 'Mã OTP đã được gửi đến số điện thoại của bạn. Mã có hiệu lực trong 2 phút.',
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
            // Trim và validate OTP code
            const trimmedOtp = String(otpCode).trim();
            
            if (!trimmedOtp || trimmedOtp.length === 0) {
                return {
                    success: false,
                    message: 'Vui lòng nhập mã OTP',
                    error: new Error('Empty OTP code')
                };
            }
            
            // ✅ THÊM: Khôi phục confirmationResult nếu chưa có
            if (!this.confirmationResult || !this.confirmationResult.verificationId) {
                this.restoreConfirmationResult();
            }
            
            // ✅ THÊM: Kiểm tra confirmationResult tồn tại
            if (!this.confirmationResult) {
                return {
                    success: false,
                    message: 'Phiên xác thực đã hết hạn. Vui lòng gửi lại mã OTP!',
                    expired: true
                };
            }
            
            if (!this.confirmationResult.verificationId) {
                return {
                    success: false,
                    message: 'Phiên xác thực không hợp lệ. Vui lòng gửi lại mã OTP!',
                    expired: true
                };
            }
    
            // Bỏ qua check isOTPExpired() - Để Firebase tự xử lý expiry
            // Firebase verification code thường hết hạn sau 1-2 phút
            // Nếu hết hạn, Firebase sẽ trả về lỗi auth/code-expired
    
            const credential = PhoneAuthProvider.credential(
                this.confirmationResult.verificationId,
                trimmedOtp
            );
            
            const result = await signInWithCredential(auth, credential);
            
            // Xóa thời gian OTP và verificationId sau khi xác thực thành công
            localStorage.removeItem('otp_sent_time');
            this.clearConfirmationResult();
            
            return {
                success: true,
                user: result.user,
                message: 'Xác thực thành công'
            };
            
        } catch (error) {
            console.error('Error verifying OTP:', error);
            
            // Xử lý lỗi code-expired từ Firebase
            if (error.code === 'auth/code-expired') {
                // Xóa verificationId khi code expired để force user phải gửi lại OTP
                this.clearConfirmationResult();
                localStorage.removeItem('otp_sent_time');
            }
            
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
            'auth/invalid-verification-code': 'Mã OTP không đúng. Vui lòng kiểm tra lại!',
            'auth/code-expired': 'Mã OTP đã hết hạn. Mã chỉ có hiệu lực trong 1-2 phút. Vui lòng yêu cầu mã mới!',
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
