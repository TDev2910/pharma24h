@extends('layouts.app')

@section('title', 'Quên Mật Khẩu')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="forgot-password-container">
        <!-- Logo Section -->
        <div class="text-center mb-4">
            <div class="logo-circle">
                <span class="logo-text">Pharma24h</span>
            </div>
        </div>

        <!-- Title -->
        <h2 class="forgot-title">Quên mật khẩu?</h2>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Success Messages -->
        @if (session('success'))
            <div class="alert alert-success mb-3">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
            @csrf
            
            <!-- Toggle between Email and Phone -->
            <div class="auth-toggle mb-4">
                <div class="toggle-buttons">
                    <button type="button" class="toggle-btn active" data-type="email">
                        <i class="fas fa-envelope me-2"></i>Email
                    </button>
                    <button type="button" class="toggle-btn" data-type="phone">
                        <i class="fas fa-phone me-2"></i>Số điện thoại
                    </button>
                </div>
            </div>

            <!-- Email Input -->
            <div class="form-group mb-4" id="email-group">
                <label for="email" class="form-label">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    name="email" 
                    value="{{ old('email') }}" 
                    autocomplete="email" 
                    autofocus
                    placeholder="Nhập địa chỉ email"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Phone Input -->
            <div class="form-group mb-4 d-none" id="phone-group">
                <label for="phone" class="form-label">Số điện thoại</label>
                <div class="input-group">
                    <span class="input-group-text">+84</span>
                    <input 
                        id="phone" 
                        type="tel" 
                        class="form-control @error('phone') is-invalid @enderror" 
                        name="phone" 
                        value="{{ old('phone') }}" 
                        autocomplete="tel"
                        placeholder="Nhập số điện thoại"
                    >
                </div>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-4" id="submitBtn">
                Gửi mã xác thực
            </button>
        </form>

        <!-- Footer Links -->
        <div class="text-center">
            <p class="footer-text">
                Không có tài khoản? 
                <a href="{{ route('register') }}" class="footer-link">Đăng ký</a>
            </p>
            <p class="footer-text">
                Đã có tài khoản? 
                <a href="{{ route('login') }}" class="footer-link">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

<style>
.forgot-password-container {
    width: 400px;
    max-width: 90vw;
    padding: 40px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

/* Logo Circle */
.logo-circle {
    width: 100px;
    height: 100px;
    background: #2b2e33;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.logo-text {
    color: white;
    font-weight: 700;
    font-size: 16px;
    letter-spacing: -0.5px;
}

/* Title */
.forgot-title {
    font-size: 28px;
    font-weight: 600;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 32px;
    letter-spacing: -0.5px;
}

/* Form Styling */
.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    height: 48px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    padding: 12px 16px;
    transition: all 0.2s ease;
    background: #f9fafb;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
    outline: none;
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.form-control.is-invalid:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Button */
.btn-primary {
    height: 48px;
    background: #4a5568;
    border: none;
    border-radius: 24px;
    font-size: 16px;
    font-weight: 600;
    color: white;
    transition: all 0.2s ease;
    letter-spacing: 0.025em;
}

.btn-primary:hover {
    background: #2b2e33;
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(74, 85, 104, 0.3);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-primary:focus {
    box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.2);
}

/* Footer Links */
.footer-text {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
}

.footer-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.footer-link:hover {
    color: #5a67d8;
    text-decoration: none;
}

/* Alert Styling */
.alert {
    border: none;
    border-radius: 8px;
    font-size: 14px;
    padding: 12px 16px;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border-left: 4px solid #dc2626;
}

.alert-success {
    background: #f0fdf4;
    color: #16a34a;
    border-left: 4px solid #16a34a;
}

.invalid-feedback {
    font-size: 12px;
    color: #ef4444;
    margin-top: 4px;
}

/* Loading State */
.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

/* Toggle Buttons */
.auth-toggle {
    margin-bottom: 24px;
}

.toggle-buttons {
    display: flex;
    background: #f3f4f6;
    border-radius: 8px;
    padding: 4px;
    gap: 4px;
}

.toggle-btn {
    flex: 1;
    padding: 12px 16px;
    border: none;
    background: transparent;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.2s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-btn:hover {
    color: #374151;
    background: rgba(255, 255, 255, 0.5);
}

.toggle-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toggle-btn i {
    font-size: 16px;
}

/* Input Group */
.input-group-text {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-right: none;
    color: #6b7280;
    font-weight: 500;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-left: none;
    box-shadow: none;
}

.input-group:focus-within .input-group-text {
    border-color: #667eea;
    background: white;
}

/* Responsive */
@media (max-width: 480px) {
    .forgot-password-container {
        padding: 24px;
        width: 100%;
        margin: 20px;
    }
    
    .forgot-title {
        font-size: 24px;
        margin-bottom: 24px;
    }
    
    .toggle-btn {
        padding: 10px 12px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const emailGroup = document.getElementById('email-group');
    const phoneGroup = document.getElementById('phone-group');
    const submitBtn = document.getElementById('submitBtn');
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    
    let currentType = 'email';
    
    // Toggle between email and phone
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Update active state
            toggleBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide inputs
            if (type === 'email') {
                emailGroup.classList.remove('d-none');
                phoneGroup.classList.add('d-none');
                emailInput.focus();
                currentType = 'email';
            } else {
                emailGroup.classList.add('d-none');
                phoneGroup.classList.remove('d-none');
                phoneInput.focus();
                currentType = 'phone';
            }
            
            // Clear validation states
            clearValidation();
        });
    });
    
    // Email validation
    emailInput.addEventListener('input', function() {
        const email = this.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && emailRegex.test(email)) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (email) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Phone validation
    phoneInput.addEventListener('input', function() {
        const phone = this.value.replace(/\D/g, '');
        this.value = phone;
        
        if (phone && phone.length >= 10) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (phone) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (currentType === 'email') {
            handleEmailSubmission();
        } else {
            handlePhoneSubmission();
        }
    });
    
    // Handle email submission (existing logic)
    function handleEmailSubmission() {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!email || !emailRegex.test(email)) {
            emailInput.focus();
            emailInput.classList.add('is-invalid');
            return;
        }
        
        // Submit form normally for email
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
        submitBtn.disabled = true;
        form.submit();
    }
    
    // Handle phone submission (Firebase)
    async function handlePhoneSubmission() {
    const phone = phoneInput.value.replace(/\D/g, '');
    
    if (!phone || phone.length < 10) {
        phoneInput.focus();
        phoneInput.classList.add('is-invalid');
        return;
    }
    
    try {
        // Import Firebase service
        const { default: firebasePhoneAuth } = await import('{{ Vite::asset("resources/js/services/firebasePhoneAuth.js") }}');
        
        // Initialize reCAPTCHA
        firebasePhoneAuth.initRecaptcha('recaptcha-container');
        
        // ✅ FORMAT ĐÚNG: Bỏ số 0 đầu nếu có
        let cleanedPhone = phone;
        if (cleanedPhone.startsWith('0')) {
            cleanedPhone = cleanedPhone.substring(1);
        }
        
        const formattedPhone = '+84' + cleanedPhone; // +84376193244 (không có số 0)
        
        console.log('Original input:', phone);
        console.log('Formatted phone:', formattedPhone);
        
        // Send OTP
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi SMS...';
        submitBtn.disabled = true;
        
        const result = await firebasePhoneAuth.sendOTP(formattedPhone);
        
        // ✅ CHỈ CHUYỂN TRANG KHI THỰC SỰ THÀNH CÔNG
        if (result.success && firebasePhoneAuth.confirmationResult) {
            // Lưu phone vào session trước khi redirect
            try {
                await fetch('/password/save-phone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone: formattedPhone })
                });
            } catch (error) {
                console.error('Failed to save phone to session:', error);
            }
            
            showSuccess(result.message);
            
            // Redirect với format đúng
            setTimeout(() => {
                window.location.href = `/password/verify-phone?phone=${encodeURIComponent(formattedPhone)}`;
            }, 1000);
        } else {
            const errorMessage = result.message || 'Không thể gửi OTP. Vui lòng thử lại!';
            showError(errorMessage);
            
            if (result.error && (result.error.code === 'auth/error-code:-39' || 
                result.error.message.includes('503') || 
                result.error.code === 'auth/quota-exceeded')) {
                
                setTimeout(() => {
                    showError('Firebase service tạm thời không khả dụng. Vui lòng sử dụng email để đặt lại mật khẩu.');
                    
                    setTimeout(() => {
                        const emailTab = document.querySelector('[data-type="email"]');
                        if (emailTab) {
                            emailTab.click();
                        }
                    }, 2000);
                }, 1000);
            }
            resetButton();
        }
        } catch (error) {
            console.error('Firebase error:', error);
            showError('Có lỗi xảy ra, vui lòng thử lại');
            resetButton();
        }
    }
    
    // Helper functions
    function clearValidation() {
        emailInput.classList.remove('is-valid', 'is-invalid');
        phoneInput.classList.remove('is-valid', 'is-invalid');
    }
    
    function showError(message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create error alert
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger mb-3';
        alert.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${message}`;
        
        // Insert before form
        form.parentNode.insertBefore(alert, form);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 5000);
    }
    
    function showSuccess(message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create success alert
        const alert = document.createElement('div');
        alert.className = 'alert alert-success mb-3';
        alert.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
        
        // Insert before form
        form.parentNode.insertBefore(alert, form);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 3000);
    }
    
    function resetButton() {
        submitBtn.innerHTML = 'Gửi mã xác thực';
        submitBtn.disabled = false;
    }
    
    // Auto-focus
    emailInput.focus();
});
</script>

<!-- reCAPTCHA Container -->
<div id="recaptcha-container"></div>
@endsection