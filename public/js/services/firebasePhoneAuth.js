import { auth } from '../config/firebase.js';
import {
    signInWithPhoneNumber,  //hàm gửi mã OTP đến số điện thoại user
    RecaptchaVerifier, //Invisible reCAPTCHA để chống spam tự động
    PhoneAuthProvider,
    signInWithCredential  //Xác thực credential và đăng nhập
} from 'firebase/auth';

class FirebasePhoneAuthService {
    constructor() {
        this.recaptchaVerifier = null;
        this.confirmationResult = null; // giữ verificationId sau khi gửi OTP
        this.otpSentTime = null;
    }

    /**
     * Khởi tạo reCAPTCHA verifier
     */
    //firebase bắt buộc phải có recaptchaVerifier đối với web để gửi OTP 
    //chống tình trạng bot spam tự động và tấn công ddos
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
     */
    async sendOTP(phoneNumber) {
        try {
            const formattedPhone = this.formatPhoneNumber(phoneNumber);

            this.confirmationResult = await signInWithPhoneNumber(
                auth,
                formattedPhone,
                this.recaptchaVerifier
            );

            // Lưu thời gian gửi OTP
            this.otpSentTime = Date.now();
            localStorage.setItem('otp_sent_time', this.otpSentTime.toString());

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
     * Xác thực mã OTP với kiểm tra đầy đủ
     */
    async verifyOTP(otpCode) {
        try {
            const trimmedOtp = String(otpCode).trim();

            if (!trimmedOtp || trimmedOtp.length === 0) {
                return {
                    success: false,
                    message: 'Vui lòng nhập mã OTP',
                    error: new Error('Empty OTP code')
                };
            }

            console.log('🔍 Verifying OTP:', {
                otpLength: trimmedOtp.length,
                hasConfirmationResult: !!this.confirmationResult,
                hasVerificationId: !!(this.confirmationResult?.verificationId)
            });

            // Kiểm tra confirmationResult tồn tại
            if (!this.confirmationResult) {
                console.error('confirmationResult is null');
                return {
                    success: false,
                    message: 'Phiên xác thực đã hết hạn. Vui lòng gửi lại mã OTP!',
                    expired: true
                };
            }

            if (!this.confirmationResult.verificationId) {
                console.error('verificationId is missing');
                return {
                    success: false,
                    message: 'Phiên xác thực không hợp lệ. Vui lòng gửi lại mã OTP!',
                    expired: true
                };
            }

            // Kiểm tra OTP có hết hạn không (5 phút)
            if (this.isOTPExpired()) {
                console.warn('OTP expired');
                return {
                    success: false,
                    message: 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.',
                    expired: true
                };
            }

            console.log('Creating credential with verificationId:', this.confirmationResult.verificationId.substring(0, 20) + '...');

            // Tạo credential và xác thực
            const credential = PhoneAuthProvider.credential(
                this.confirmationResult.verificationId,
                trimmedOtp
            );

            console.log('Credential created, signing in...');

            const result = await signInWithCredential(auth, credential);

            console.log('Firebase signInWithCredential successful');
            console.log('User UID:', result.user?.uid);

            // Xóa thời gian OTP sau khi xác thực thành công
            localStorage.removeItem('otp_sent_time');
            this.otpSentTime = null;

            return {
                success: true,
                user: result.user,
                message: 'Xác thực thành công'
            };

        } catch (error) {
            console.error('Error verifying OTP:', error);
            console.error('Error details:', {
                code: error.code,
                message: error.message,
                stack: error.stack
            });

            // Log chi tiết để debug
            console.log('OTP Verification Debug:', {
                otpCode: otpCode,
                trimmedOtp: String(otpCode).trim(),
                confirmationResult: !!this.confirmationResult,
                verificationId: this.confirmationResult?.verificationId?.substring(0, 20) + '...',
                errorCode: error.code,
                errorMessage: error.message
            });

            return {
                success: false,
                message: this.getErrorMessage(error.code),
                error: error
            };
        }
    }

    /**
     * Kiểm tra OTP có hết hạn không
     */
    isOTPExpired() {
        const sentTime = this.otpSentTime || localStorage.getItem('otp_sent_time');
        if (!sentTime) return false;

        const now = Date.now();
        const timeDiff = now - parseInt(sentTime);
        const fiveMinutes = 5 * 60 * 1000; // 5 phút

        return timeDiff > fiveMinutes;
    }

    /**
     * Format số điện thoại Việt Nam thành +84
     */
    formatPhoneNumber(phoneNumber) {
        let cleaned = phoneNumber.replace(/\D/g, '');

        if (cleaned.startsWith('84')) {
            cleaned = cleaned.substring(2);
        } else if (cleaned.startsWith('0')) {
            cleaned = cleaned.substring(1);
        }

        const last9Digits = cleaned.slice(-9);
        return '+84' + last9Digits;
    }

    /**
     *Thông báo lỗi
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
            'auth/network-request-failed': 'Lỗi kết nối mạng',
            'auth/session-expired': 'Phiên làm việc đã hết hạn'
        };

        return errorMessages[errorCode] || 'Có lỗi xảy ra, vui lòng thử lại';
    }

    /**
     * Reset trạng thái để gửi OTP mới
     */
    reset() {
        this.confirmationResult = null;
        this.otpSentTime = null;
        localStorage.removeItem('otp_sent_time');
    }

    /**
     * Dọn dẹp reCAPTCHA
     */
    cleanup() {
        if (this.recaptchaVerifier) {
            this.recaptchaVerifier.clear();
            this.recaptchaVerifier = null;
        }
        this.reset();
    }
}

// Export singleton instance
export default new FirebasePhoneAuthService();