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
            
            <div class="form-group mb-4">
                <label for="email" class="form-label">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email" 
                    autofocus
                    placeholder=""
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-4">
                Quên mật khẩu
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
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const emailInput = document.getElementById('email');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Enhanced form validation
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
    
    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!email || !emailRegex.test(email)) {
            e.preventDefault();
            emailInput.focus();
            emailInput.classList.add('is-invalid');
            return;
        }
        
        // Loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;
    });
    
    // Auto-focus and smooth animations
    emailInput.focus();
});
</script>
@endsection