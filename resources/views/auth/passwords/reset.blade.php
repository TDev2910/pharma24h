@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="reset-password-container">
        <!-- Logo Section -->
        <div class="text-center mb-4">
            <div class="logo-circle">
                <span class="logo-text">Pharma24h</span>
            </div>
        </div>

        <!-- Title -->
        <h2 class="reset-title">Tạo mật khẩu mới</h2>
        <p class="reset-subtitle">
            Nhập mật khẩu mới cho tài khoản của bạn<br>
            <strong class="email-highlight">{{ $email }}</strong>
        </p>

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

        <!-- Reset Password Form -->
        <form method="POST" action="{{ route('password.reset.post') }}" id="resetPasswordForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group mb-3">
                <label for="password" class="form-label">Mật khẩu mới</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Enter your new password"
                       required 
                       minlength="8"
                       autocomplete="new-password">
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Mật khẩu phải có ít nhất 8 ký tự
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" 
                       name="password_confirmation" 
                       id="confirmPassword" 
                       class="form-control" 
                       placeholder="Confirm your new password"
                       required 
                       minlength="8"
                       autocomplete="new-password">
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Nhập lại mật khẩu để xác nhận
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-4" id="resetBtn">
                Cập nhật mật khẩu
            </button>
        </form>

        <!-- Footer Links -->
        <div class="text-center">
            <p class="footer-text">
                <a href="{{ route('login') }}" class="footer-link">Quay lại đăng nhập</a>
            </p>
        </div>
    </div>
</div>

<style>
.reset-password-container {
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
.reset-title {
    font-size: 28px;
    font-weight: 600;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 16px;
    letter-spacing: -0.5px;
}

.reset-subtitle {
    font-size: 14px;
    color: #6b7280;
    text-align: center;
    margin-bottom: 32px;
    line-height: 1.5;
}

.email-highlight {
    color: #667eea;
    font-weight: 600;
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

.form-control.is-valid {
    border-color: #10b981;
    background: #f0fdf4;
}

.form-control.is-valid:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
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

/* Password Strength Indicator */
.password-strength {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    margin-top: 8px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.strength-weak { background: #ef4444; width: 25%; }
.strength-fair { background: #f59e0b; width: 50%; }
.strength-good { background: #10b981; width: 75%; }
.strength-strong { background: #059669; width: 100%; }

/* Responsive */
@media (max-width: 480px) {
    .reset-password-container {
        padding: 24px;
        width: 100%;
        margin: 20px;
    }
    
    .reset-title {
        font-size: 24px;
        margin-bottom: 24px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirmPassword');
    const resetBtn = document.getElementById('resetBtn');
    
    // Auto-focus password input
    passwordInput.focus();
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        return Math.min(strength, 4);
    }
    
    // Real-time password validation
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        
        // Visual feedback
        if (password.length === 0) {
            this.classList.remove('is-valid', 'is-invalid');
        } else if (password.length < 8) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
        
        // Check password match
        checkPasswordMatch();
    });
    
    // Real-time password confirmation check
    function checkPasswordMatch() {
        if (confirmInput.value && passwordInput.value !== confirmInput.value) {
            confirmInput.setCustomValidity('Passwords do not match');
            confirmInput.classList.add('is-invalid');
            confirmInput.classList.remove('is-valid');
        } else if (confirmInput.value && passwordInput.value === confirmInput.value) {
            confirmInput.setCustomValidity('');
            confirmInput.classList.remove('is-invalid');
            confirmInput.classList.add('is-valid');
        } else {
            confirmInput.setCustomValidity('');
            confirmInput.classList.remove('is-valid', 'is-invalid');
        }
    }
    
    confirmInput.addEventListener('input', checkPasswordMatch);
    
    // Enhanced form submission
    form.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmInput.value;
        
        // Validation
        if (password.length < 8) {
            e.preventDefault();
            passwordInput.focus();
            passwordInput.setCustomValidity('Password must be at least 8 characters');
            passwordInput.reportValidity();
            return;
        }
        
        if (password !== confirmPassword) {
            e.preventDefault();
            confirmInput.focus();
            confirmInput.setCustomValidity('Passwords do not match');
            confirmInput.reportValidity();
            return;
        }
        
        // Loading state
        resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        resetBtn.disabled = true;
    });
    
    // Clear validation errors on focus
    [passwordInput, confirmInput].forEach(input => {
        input.addEventListener('focus', function() {
            this.setCustomValidity('');
        });
    });
});
</script>
@endsection
