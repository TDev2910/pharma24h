/**
 * Admin Notifications Handler
 * Xử lý thông báo thành công/lỗi cho admin panel
 * Tránh duplicate code giữa các modules
 */
class AdminNotifications {
    static initialized = false;
    
    /**
     * Khởi tạo notifications
     */
    static init() {
        // Chỉ khởi tạo một lần
        if (this.initialized) {
            console.log('AdminNotifications already initialized');
            return;
        }
        
        this.initialized = true;
        console.log('AdminNotifications initialized');
        
        this.initSweetAlert();
        this.handleFlashMessages();
    }
    
    /**
     * Kiểm tra SweetAlert2 đã load chưa
     */
    static initSweetAlert() {
        if (typeof Swal === 'undefined') {
            console.warn('SweetAlert2 not loaded. Please include SweetAlert2 CDN.');
            return false;
        }
        return true;
    }
    
    /**
     * Xử lý flash messages từ server
     */
    static handleFlashMessages() {
        const successMessage = this.getFlashMessage('success');
        const errorMessage = this.getFlashMessage('error');
        
        if (successMessage) {
            this.showSuccess(successMessage);
        }
        
        if (errorMessage) {
            this.showError(errorMessage);
        }
    }
    
    /**
     * Lấy flash message từ meta tag
     */
    static getFlashMessage(type) {
        const metaTag = document.querySelector(`meta[name="flash-${type}"]`);
        return metaTag ? metaTag.getAttribute('content') : null;
    }
    
    /**
     * Hiển thị thông báo thành công
     */
    static showSuccess(message) {
        if (!this.initSweetAlert()) {
            // Fallback nếu không có SweetAlert2
            alert('Thành công: ' + message);
            return;
        }
        
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: message,
            showConfirmButton: false,
            timer: 1500,
            toast: false,
            position: 'center'
        });
    }
    
    /**
     * Hiển thị thông báo lỗi
     */
    static showError(message) {
        if (!this.initSweetAlert()) {
            // Fallback nếu không có SweetAlert2
            alert('Lỗi: ' + message);
            return;
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: message,
            confirmButtonText: 'Đóng'
        });
    }
    
    /**
     * Hiển thị thông báo xác nhận
     */
    static showConfirm(title, text, callback) {
        if (!this.initSweetAlert()) {
            // Fallback nếu không có SweetAlert2
            if (confirm(title + '\n' + text)) {
                callback();
            }
            return;
        }
        
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    }
    
    /**
     * Hiển thị thông báo loading
     */
    static showLoading(title = 'Đang xử lý...') {
        if (!this.initSweetAlert()) {
            return;
        }
        
        Swal.fire({
            title: title,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    /**
     * Đóng thông báo loading
     */
    static hideLoading() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }
    
    /**
     * Hiển thị toast notification
     */
    static showToast(message, type = 'success') {
        if (!this.initSweetAlert()) {
            alert(message);
            return;
        }
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
}

// Auto-initialize khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    AdminNotifications.init();
});

// Export để sử dụng global
window.AdminNotifications = AdminNotifications;
