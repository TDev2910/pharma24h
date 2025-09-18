@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100 py-4">
    <div class="register-container">
        <!-- Logo Section -->
        <div class="text-center mb-4">
            <div class="logo-circle">
                <span class="logo-text">Pharma24h</span>
            </div>
        </div>

        <!-- Title -->
        <h2 class="register-title">Tạo tài khoản</h2>
        <p class="register-subtitle">
            Tham gia ngay! Vui lòng điền thông tin của bạn
        </p>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf
            
            <!-- Name Input -->
            <div class="form-group mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="Nhập họ và tên"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Input -->
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="Nhập email"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Row -->
            <div class="row mb-3">
                <div class="col-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Nhập mật khẩu"
                               required 
                               minlength="8">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm</label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Nhập lại mật khẩu"
                               required 
                               minlength="8">
                    </div>
                </div>
            </div>

            <!-- Phone Input -->
            <div class="form-group mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="tel" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       placeholder="Nhập số điện thoại"
                       required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Address (Optional) -->
            <div class="form-group mb-4">
                <label for="address" class="form-label">Địa chỉ (tùy chọn)</label>
                <input type="text" 
                       class="form-control @error('address') is-invalid @enderror" 
                       id="address" 
                       name="address" 
                       value="{{ old('address') }}" 
                       placeholder="Nhập địa chỉ (có thể để trống)">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn btn-primary w-100 mb-4" id="registerBtn">
                Tạo tài khoản
            </button>
        </form>

        <!-- Footer Links -->
        <div class="text-center">
            <p class="footer-text">
                Đã có tài khoản? 
                <a href="{{ route('login') }}" class="footer-link">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

<style>
.register-container {
    width: 450px;
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
.register-title {
    font-size: 28px;
    font-weight: 600;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 16px;
    letter-spacing: -0.5px;
}

.register-subtitle {
    font-size: 14px;
    color: #6b7280;
    text-align: center;
    margin-bottom: 32px;
    line-height: 1.5;
}

/* Form Styling */
.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
}

.form-control, .form-select {
    height: 48px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    padding: 12px 16px;
    transition: all 0.2s ease;
    background: #f9fafb;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
    outline: none;
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #ef4444;
}

.form-control.is-invalid:focus, .form-select.is-invalid:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-control.is-valid, .form-select.is-valid {
    border-color: #10b981;
    background: #f0fdf4;
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

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
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

.invalid-feedback {
    font-size: 12px;
    color: #ef4444;
    margin-top: 4px;
}

/* Responsive */
@media (max-width: 480px) {
    .register-container {
        padding: 24px;
        width: 100%;
        margin: 20px;
    }
    
    .register-title {
        font-size: 24px;
        margin-bottom: 24px;
    }
    
    .row .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const registerBtn = document.getElementById('registerBtn');
    
    // Auto-focus name input
    nameInput.focus();
    
    // Real-time validation
    nameInput.addEventListener('input', function() {
        if (this.value.trim().length >= 2) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });
    
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
    
    phoneInput.addEventListener('input', function() {
        const phone = this.value;
        const phoneRegex = /^[0-9]{10,11}$/;
        
        if (phone && phoneRegex.test(phone)) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (phone) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length >= 8) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (password.length > 0) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
        
        validatePasswordMatch();
    });
    
            function validatePasswordMatch() {
        if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Mật khẩu xác nhận không khớp');
            confirmPasswordInput.classList.add('is-invalid');
            confirmPasswordInput.classList.remove('is-valid');
        } else if (confirmPasswordInput.value && passwordInput.value === confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('');
            confirmPasswordInput.classList.remove('is-invalid');
            confirmPasswordInput.classList.add('is-valid');
        } else {
            confirmPasswordInput.setCustomValidity('');
            confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
        }
    }
    
    confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{10,11}$/;
        
        // Basic validation
        if (!name || name.length < 2) {
            e.preventDefault();
            nameInput.focus();
            nameInput.classList.add('is-invalid');
            return;
        }
        
        if (!email || !emailRegex.test(email)) {
            e.preventDefault();
            emailInput.focus();
            emailInput.classList.add('is-invalid');
            return;
        }
        
        if (!phone || !phoneRegex.test(phone)) {
            e.preventDefault();
            phoneInput.focus();
            phoneInput.classList.add('is-invalid');
            return;
        }
        
        if (!password || password.length < 8) {
            e.preventDefault();
            passwordInput.focus();
            passwordInput.classList.add('is-invalid');
            return;
        }
        
        if (password !== confirmPassword) {
            e.preventDefault();
            confirmPasswordInput.focus();
            confirmPasswordInput.classList.add('is-invalid');
            return;
        }
        
        // Loading state
        registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo tài khoản...';
        registerBtn.disabled = true;
    });
});
</script>
@endsection