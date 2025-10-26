import { auth } from '../config/firebase.js';
import { 
    signInWithPhoneNumber, 
    RecaptchaVerifier,
    PhoneAuthProvider,
    signInWithCredential
} from 'firebase/auth';

class FirebasePhoneAuthService {
    constructor() {
        this.recaptchaVerifier = null;
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
                console.log('reCAPTCHA solved');
            },
            'expired-callback': () => {
                console.log('reCAPTCHA expired');
            }
        });
    }

    /**
     * Gửi mã OTP đến số điện thoại
     * @param {string} phoneNumber - Số điện thoại (format: +84xxxxxxxxx)
     * @returns {Promise} - Promise với confirmation result
     */
    async sendOTP(phoneNumber) {
        try {
            // Đảm bảo phone number có format đúng
            const formattedPhone = this.formatPhoneNumber(phoneNumber);
            
            // Gửi OTP
            this.confirmationResult = await signInWithPhoneNumber(
                auth, 
                formattedPhone, 
                this.recaptchaVerifier
            );
            
            return {
                success: true,
                message: 'Mã OTP đã được gửi đến số điện thoại của bạn'
            };
        } catch (error) {
            console.error('Error sending OTP:', error);
            return {
                success: false,
                message: this.getErrorMessage(error.code)
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
            const credential = PhoneAuthProvider.credential(
                this.confirmationResult.verificationId,
                otpCode
            );
            
            const result = await signInWithCredential(auth, credential);
            
            return {
                success: true,
                user: result.user,
                message: 'Xác thực thành công'
            };
        } catch (error) {
            console.error('Error verifying OTP:', error);
            return {
                success: false,
                message: this.getErrorMessage(error.code)
            };
        }
    }

    /**
     * Format số điện thoại Việt Nam
     * @param {string} phoneNumber - Số điện thoại
     * @returns {string} - Số điện thoại đã format
     */
    formatPhoneNumber(phoneNumber) {
        let cleaned = phoneNumber.replace(/\D/g, '');

        if(cleaned.startsWith('84')) {
            cleaned = cleaned.substring(2);
        }

        else if(cleaned.startsWith('0')) {
            cleaned = cleaned.substring(1);
        }

        const last9Digits = cleaned.slice(-9);
        return '+84' + last9Digits;
    }

    /**
     * Lấy thông báo lỗi thân thiện
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
