import { auth } from './firebase.js'; // Đảm bảo file firebase.js đã dùng import.meta.env
import {
    GoogleAuthProvider,
    signInWithPopup
} from 'firebase/auth';

class FirebaseGoogleAuthService {
    constructor() {
        this.provider = new GoogleAuthProvider();
        // Thêm dòng này để ép buộc Google luôn hiện màn hình chọn tài khoản
        // Giúp tránh lỗi tự động đăng nhập sai tài khoản cũ
        this.provider.setCustomParameters({
            prompt: 'select_account'
        });
    }

    /**
     * Đăng nhập với Google
     * @returns {Promise} - Promise với kết quả đăng nhập
     */
    async signInWithGoogle() {
        try {
            const result = await signInWithPopup(auth, this.provider);
            const user = result.user;

            // Lấy idToken
            const idToken = await user.getIdToken();

            return {
                success: true,
                user: {
                    uid: user.uid,
                    email: user.email,
                    name: user.displayName,
                    photoURL: user.photoURL,
                },
                idToken: idToken,
                message: 'Đăng nhập thành công'
            };
        } catch (error) {
            // Xử lý lỗi account-exists-with-different-credential
            if (error.code === 'auth/account-exists-with-different-credential') {
                return {
                    success: false,
                    message: 'Email này đã được sử dụng với phương thức đăng nhập khác',
                    error: error,
                    code: error.code
                };
            }

            // Xử lý lỗi popup closed
            if (error.code === 'auth/popup-closed-by-user') {
                return {
                    success: false,
                    message: 'Bạn đã đóng cửa sổ đăng nhập',
                    error: error,
                    code: error.code
                };
            }

            return {
                success: false,
                message: this.getErrorMessage(error.code),
                error: error
            };
        }
    }

    /**
     * Lấy thông báo lỗi
     * @param {string} errorCode - Mã lỗi Firebase
     * @returns {string} - Thông báo lỗi
     */
    getErrorMessage(errorCode) {
        const errorMessages = {
            'auth/popup-closed-by-user': 'Bạn đã đóng cửa sổ đăng nhập',
            'auth/cancelled-popup-request': 'Yêu cầu đăng nhập đã bị hủy',
            'auth/account-exists-with-different-credential': 'Email này đã được sử dụng với phương thức đăng nhập khác',
            'auth/operation-not-allowed': 'Phương thức đăng nhập này chưa được kích hoạt',
            'auth/network-request-failed': 'Lỗi kết nối mạng hoặc bị chặn bởi trình duyệt (AdBlock)',
            'auth/popup-blocked': 'Cửa sổ popup bị chặn. Vui lòng cho phép popup cho trang web này',
            'auth/unauthorized-domain': 'Tên miền này chưa được phép (Unauthorized Domain). Vui lòng kiểm tra Firebase Console.'
        };

        return errorMessages[errorCode] || `Có lỗi xảy ra (${errorCode}), vui lòng thử lại`;
    }
}

// Khởi tạo instance
const firebaseGoogleAuthService = new FirebaseGoogleAuthService();

// --- QUAN TRỌNG: Gắn vào Window để Blade gọi được ---
// Thay vì export default và chờ import, ta công khai nó ra toàn cục
if (typeof window !== 'undefined') {
    window.FirebaseGoogleAuthService = firebaseGoogleAuthService;
}

export default firebaseGoogleAuthService;
