@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
{{-- QUAN TRỌNG: Load file JS này để nó chạy và gắn vào window --}}
@vite(['resources/js/library/firebaseGoogleAuth.js'])

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="login-container">
        <div class="text-center mb-4">
            <div class="logo-circle">
                <span class="logo-text">Pharma24h</span>
            </div>
        </div>

        <h2 class="login-title">Đăng nhập vào tài khoản</h2>
        <p class="login-subtitle">
            Chào mừng bạn quay trở lại! Vui lòng nhập thông tin của bạn
        </p>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="Nhập email"
                       required
                       autocomplete="email"
                       autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       placeholder="Nhập mật khẩu"
                       required
                       autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="remember"
                           name="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Ghi nhớ tôi
                    </label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-password-link">
                    Quên mật khẩu?
                </a>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-4" id="loginBtn">
                Đăng nhập
            </button>
            <div class="text-center my-4">
                <span class="text-muted">Hoặc</span>
            </div>

            <button type="button" class="btn btn-outline-danger w-100 mb-3" id="googleLoginBtn">
                <svg width="18" height="18" viewBox="0 0 18 18" style="margin-right: 8px;">
                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.96-2.186l-2.908-2.258c-.806.54-1.837.86-3.052.86-2.347 0-4.33-1.584-5.04-3.71H.957v2.331C2.438 15.983 5.482 18 9 18z"/>
                    <path fill="#FBBC05" d="M3.96 10.71c-.18-.54-.282-1.117-.282-1.71s.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.04l3.003-2.33z"/>
                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.96L3.96 7.29C4.67 5.163 6.653 3.58 9 3.58z"/>
                </svg>
                Đăng nhập với Google
            </button>
        </form>

        <div class="text-center">
            <p class="footer-text">
                Không có tài khoản?
                <a href="{{ route('register') }}" class="footer-link">Đăng ký</a>
            </p>
        </div>
    </div>
</div>

<style>
/* ... (Giữ nguyên phần style của bạn) ... */
.login-container { width: 400px; max-width: 90vw; padding: 40px; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
.logo-circle { width: 100px; height: 100px; background: #2b2e33; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; }
.logo-text { color: white; font-weight: 700; font-size: 16px; letter-spacing: -0.5px; }
.login-title { font-size: 28px; font-weight: 600; color: #1a1a1a; text-align: center; margin-bottom: 16px; letter-spacing: -0.5px; }
.login-subtitle { font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 32px; line-height: 1.5; }
.form-label { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: block; }
.form-control { height: 48px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; padding: 12px 16px; transition: all 0.2s ease; background: #f9fafb; }
.form-control:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); background: white; outline: none; }
.form-control.is-invalid { border-color: #ef4444; }
.form-control.is-invalid:focus { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
.form-check { display: flex; align-items: center; }
.form-check-input { margin-right: 8px; width: 16px; height: 16px; }
.form-check-label { font-size: 14px; color: #374151; cursor: pointer; }
.forgot-password-link { font-size: 14px; color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.2s ease; }
.forgot-password-link:hover { color: #5a67d8; text-decoration: none; }
.btn-primary { height: 48px; background: #4a5568; border: none; border-radius: 24px; font-size: 16px; font-weight: 600; color: white; transition: all 0.2s ease; letter-spacing: 0.025em; }
.btn-primary:hover { background: #2b2e33; transform: translateY(-1px); box-shadow: 0 8px 25px rgba(74, 85, 104, 0.3); }
.btn-primary:disabled { background: #9ca3af; cursor: not-allowed; transform: none; }
.footer-text { font-size: 14px; color: #6b7280; margin-bottom: 8px; }
.footer-link { color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.2s ease; }
.footer-link:hover { color: #5a67d8; text-decoration: none; }
.alert { border: none; border-radius: 8px; font-size: 14px; padding: 12px 16px; }
.alert-danger { background: #fef2f2; color: #dc2626; border-left: 4px solid #dc2626; }
.invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 4px; }
.btn-outline-danger { height: 48px; border: 2px solid #ea4335; border-radius: 24px; font-size: 16px; font-weight: 600; color: #ea4335; background: white; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; }
.btn-outline-danger:hover { background: #ea4335; color: white; transform: translateY(-1px); box-shadow: 0 8px 25px rgba(234, 67, 53, 0.3); }
.btn-outline-danger:disabled { background: #f5f5f5; border-color: #d0d0d0; color: #9ca3af; cursor: not-allowed; transform: none; }
@media (max-width: 480px) { .login-container { padding: 24px; width: 100%; margin: 20px; } .login-title { font-size: 24px; margin-bottom: 24px; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');

    // Auto-focus email input
    emailInput.focus();

    // Enhanced email validation
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

    // Password validation
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        if (password.length >= 1) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email || !emailRegex.test(email)) {
            e.preventDefault();
            emailInput.focus();
            emailInput.classList.add('is-invalid');
            return;
        }

        if (!password) {
            e.preventDefault();
            passwordInput.focus();
            passwordInput.classList.add('is-invalid');
            return;
        }

        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang đăng nhập...';
        loginBtn.disabled = true;
    });

    // Clear validation errors on focus
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
        });
    });

    const googleLoginBtn = document.getElementById('googleLoginBtn');

    if (googleLoginBtn) {
        googleLoginBtn.addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerHTML;

            try {
                // Disable button và hiển thị loading
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';

                // --- THAY ĐỔI: Sử dụng Global Object từ window thay vì Dynamic Import ---
                if (typeof window.FirebaseGoogleAuthService === 'undefined') {
                    // Nếu mạng chậm, có thể script chưa load xong
                    throw new Error('Dịch vụ Google chưa tải xong. Vui lòng tải lại trang và thử lại.');
                }

                // Gọi service đã được load sẵn
                const result = await window.FirebaseGoogleAuthService.signInWithGoogle();

                if (!result.success) {
                    throw new Error(result.message || 'Đăng nhập thất bại');
                }

                // Lấy CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error('CSRF token không tìm thấy');
                }

                // Gửi thông tin lên backend
                const formData = new FormData();
                formData.append('idToken', result.idToken);
                formData.append('uid', result.user.uid);
                formData.append('email', result.user.email);
                formData.append('name', result.user.name);
                formData.append('photoURL', result.user.photoURL || '');
                formData.append('_token', csrfToken);

                const response = await fetch('{{ route("auth.google") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                let data;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    throw new Error('Phản hồi từ server không hợp lệ');
                }

                if (response.ok && data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    throw new Error(data.message || 'Đăng nhập thất bại');
                }

            } catch (error) {
                // log removed

                let errorMessage = 'Có lỗi xảy ra khi đăng nhập với Google';
                if (error.message) {
                    errorMessage = error.message;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi đăng nhập',
                        text: errorMessage
                    });
                } else {
                    alert(errorMessage);
                }

                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }
});
</script>
@endsection
