@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="verify-otp-container">
        <!-- Logo Section -->
        <div class="text-center mb-4">
            <div class="logo-circle">
                <span class="logo-text">Pharma24h</span>
            </div>
        </div>

        <!-- Title -->
        <h2 class="verify-title">Nhập mã OTP</h2>
        <p class="verify-subtitle">
            Firebase App đã gửi mã OTP đến:<br>
            <strong class="phone-highlight">{{ $phone }}</strong><br>
                <small class="text-muted">Mã OTP có hiệu lực trong <span id="otp-timer">2:00</span> phút</small>
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

        <!-- OTP Form -->
        <form method="POST" action="{{ route('password.verify.phone.post') }}" id="otpForm">
            @csrf
            <input type="hidden" name="phone" value="{{ $phone }}">
            <input type="hidden" name="otp" id="hiddenOtp">

            <!-- 6 OTP Input Boxes -->
            <div class="otp-inputs-container mb-4">
                <input type="text" class="otp-input" maxlength="1" data-index="0" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="1" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="2" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="3" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="4" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="5" autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-4" id="verifyBtn">
                Xác thực mã OTP
            </button>
        </form>

        <!-- Resend Section -->
        <div class="text-center">
            <p class="footer-text">
                Chưa nhận được mã OTP?
                <button type="button" class="resend-link" id="resendBtn" disabled>
                    <span id="resendText">Gửi lại mã OTP</span>
                    <span id="countdownText" class="d-none">Gửi lại sau <span id="countdown">60</span>s</span>
                </button>
            </p>
            <p class="footer-text">
                <a href="{{ route('password.request') }}" class="footer-link">Quay lại</a>
            </p>
        </div>
    </div>
</div>

<style>
.verify-otp-container {
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
.verify-title {
    font-size: 28px;
    font-weight: 600;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 16px;
    letter-spacing: -0.5px;
}

.verify-subtitle {
    font-size: 14px;
    color: #6b7280;
    text-align: center;
    margin-bottom: 32px;
    line-height: 1.5;
}

.phone-highlight {
    color: #667eea;
    font-weight: 600;
}

/* OTP Inputs Container */
.otp-inputs-container {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 32px;
}

/* Individual OTP Input */
.otp-input {
    width: 50px;
    height: 60px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    color: #1a1a1a;
    background: #f9fafb;
    transition: all 0.2s ease;
    outline: none;
}

.otp-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
    transform: scale(1.05);
}

.otp-input.filled {
    border-color: #10b981;
    background: #f0fdf4;
    color: #059669;
}

.otp-input.error {
    border-color: #ef4444;
    background: #fef2f2;
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
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

.resend-link {
    background: none;
    border: none;
    color: #667eea;
    font-weight: 500;
    cursor: pointer;
    text-decoration: underline;
    transition: color 0.2s ease;
    padding: 0;
    font-size: 14px;
}

.resend-link:hover:not(:disabled) {
    color: #5a67d8;
}

.resend-link:disabled {
    color: #9ca3af;
    cursor: not-allowed;
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

/* Responsive */
@media (max-width: 480px) {
    .verify-otp-container {
        padding: 24px;
        width: 100%;
        margin: 20px;
    }

    .verify-title {
        font-size: 24px;
        margin-bottom: 24px;
    }

    .otp-input {
        width: 45px;
        height: 50px;
        font-size: 20px;
    }

    .otp-inputs-container {
        gap: 6px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.otp-input');
    const form = document.getElementById('otpForm');
    const hiddenOtp = document.getElementById('hiddenOtp');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const resendText = document.getElementById('resendText');
    const countdownText = document.getElementById('countdownText');
    const countdown = document.getElementById('countdown');

    let countdownTimer = null;
    let countdownValue = 60;
    let otpTimer = null;
    let otpTimeLeft = 2 * 60; // 2 phút (Firebase thực tế chỉ cho phép 1-2 phút)

    // Focus first input
    otpInputs[0].focus();

    // Start countdown
    startCountdown();
    startOTPTimer();

    // Handle input events
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;

            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Add filled class
            if (value) {
                e.target.classList.add('filled');
                e.target.classList.remove('error');
            } else {
                e.target.classList.remove('filled');
            }

            // Move to next input
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            // Update hidden input and check completion
            updateOtpValue();
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].classList.remove('filled');
                }
                e.target.classList.remove('error');
                updateOtpValue();
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = e.clipboardData.getData('text');
            const numbers = paste.replace(/\D/g, '').slice(0, 6);

            // Fill inputs with pasted numbers
            numbers.split('').forEach((num, i) => {
                if (i < otpInputs.length) {
                    otpInputs[i].value = num;
                    otpInputs[i].classList.add('filled');
                }
            });

            // Focus appropriate input
            const nextIndex = Math.min(numbers.length, otpInputs.length - 1);
            otpInputs[nextIndex].focus();

            updateOtpValue();
        });
    });

    // Update hidden OTP value
    function updateOtpValue() {
        const otp = Array.from(otpInputs).map(input => input.value).join('');
        hiddenOtp.value = otp;

        // Auto submit when 6 digits entered
        if (otp.length === 6) {
            setTimeout(() => {
                if (hiddenOtp.value.length === 6) {
                    verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác thực...';
                    verifyBtn.disabled = true;
                    // Use dispatchEvent to ensure event listener is triggered
                    const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                    form.dispatchEvent(submitEvent);
                }
            }, 500);
        }
    }

    // Form submission
    form.addEventListener('submit', async function(e) {
        const otp = hiddenOtp.value;

        if (otp.length !== 6) {
            e.preventDefault();

            // Show error on empty inputs
            otpInputs.forEach(input => {
                if (!input.value) {
                    input.classList.add('error');
                }
            });

            // Focus first empty input
            const firstEmpty = Array.from(otpInputs).find(input => !input.value);
            if (firstEmpty) {
                firstEmpty.focus();
            }

            return;
        }

        e.preventDefault(); // Prevent default form submission

        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác thực...';
        verifyBtn.disabled = true;

        try {
            let firebasePhoneAuth;
            try {
                const module = await import('{{ Vite::asset("resources/js/library/firebasePhoneAuth.js") }}');
                firebasePhoneAuth = module.default;
            } catch (importError) {
                console.error('Failed to import Firebase service:', importError);
                throw new Error('Không thể tải Firebase service: ' + importError.message);
            }

            if (!firebasePhoneAuth || typeof firebasePhoneAuth.verifyOTP !== 'function') {
                throw new Error('Firebase service không hợp lệ: verifyOTP method không tồn tại');
            }

            const verifyResult = await firebasePhoneAuth.verifyOTP(otp);

            if (!verifyResult.success) {
                // OTP sai - hiển thị lỗi từ Firebase
                verifyBtn.innerHTML = 'Xác thực mã OTP';
                verifyBtn.disabled = false;

                // Show error animation
                otpInputs.forEach(input => {
                    input.classList.add('error');
                });

                // Refresh CSRF token và gửi thông báo lỗi lên backend để đếm attempts
                await refreshCSRFToken();

                // Gửi request lên backend để đếm số lần thử sai
                const formData = new FormData();
                formData.append('phone', document.querySelector('input[name="phone"]').value);
                formData.append('otp', otp);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const errorResponse = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                // Parse error response để lấy thông báo về số lần thử còn lại
                try {
                    if (errorResponse.headers.get('content-type')?.includes('application/json')) {
                        const errorData = await errorResponse.json();
                        if (errorData.message) {
                            showMessage(errorData.message, 'error');
                        } else {
                            showMessage(verifyResult.message || 'Mã OTP không đúng. Vui lòng thử lại!', 'error');
                        }
                    } else {
                        const errorText = await errorResponse.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(errorText, 'text/html');
                        const errorElement = doc.querySelector('.alert-danger, .error');

                        if (errorElement) {
                            showMessage(errorElement.textContent.trim(), 'error');
                        } else {
                            showMessage(verifyResult.message || 'Mã OTP không đúng. Vui lòng thử lại!', 'error');
                        }
                    }
                } catch (parseError) {
                    console.error('Error parsing response:', parseError);
                    showMessage(verifyResult.message || 'Mã OTP không đúng. Vui lòng thử lại!', 'error');
                }

                return;
            }

            // ✅ BƯỚC 2: OTP đúng - Lấy uid và idToken từ Firebase
            const user = verifyResult.user;

            if (!user) {
                console.error('User is null after verification');
                showMessage('Lỗi xác thực: Không thể lấy thông tin người dùng', 'error');
                verifyBtn.innerHTML = 'Xác thực mã OTP';
                verifyBtn.disabled = false;
                return;
            }

            try {
                // Lấy idToken
                const idToken = await user.getIdToken();

                if (!idToken) {
                    throw new Error('Không thể lấy idToken từ Firebase');
                }

                // ✅ BƯỚC 3: Gửi uid và idToken lên backend
                await refreshCSRFToken();

                const formData = new FormData();
                formData.append('phone', document.querySelector('input[name="phone"]').value);
                formData.append('uid', user.uid);
                formData.append('idToken', idToken);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const contentType = response.headers.get('content-type') || '';

                    if (contentType.includes('application/json')) {
                        const data = await response.json();

                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                            return;
                        }
                    }

                    // Fallback: redirect đến trang reset password
                    window.location.href = '/password/reset-password';

                } else {
                    // Handle error
                    const contentType = response.headers.get('content-type') || '';

                    let errorMessage = 'Có lỗi xảy ra, vui lòng thử lại';

                    try {
                        if (contentType.includes('application/json')) {
                            const errorData = await response.json();
                            errorMessage = errorData.message || errorMessage;
                        } else {
                            const responseText = await response.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(responseText, 'text/html');
                            const errorElement = doc.querySelector('.alert-danger, .error');

                            if (errorElement) {
                                errorMessage = errorElement.textContent.trim();
                            }
                        }
                    } catch (parseError) {
                        console.error('Error parsing error response:', parseError);
                    }

                    showMessage(errorMessage, 'error');
                    verifyBtn.innerHTML = 'Xác thực mã OTP';
                    verifyBtn.disabled = false;
                }

            } catch (tokenError) {
                console.error('Error getting idToken:', tokenError);
                showMessage('Lỗi xác thực: ' + (tokenError.message || 'Không thể lấy token'), 'error');
                verifyBtn.innerHTML = 'Xác thực mã OTP';
                verifyBtn.disabled = false;
            }
        } catch (error) {
            console.error('Verification error:', error);

            // Hiển thị lỗi chi tiết hơn
            let errorMessage = 'Có lỗi xảy ra, vui lòng thử lại';
            if (error.message) {
                errorMessage = error.message;
            }

            showMessage(errorMessage, 'error');
            verifyBtn.innerHTML = 'Xác thực mã OTP';
            verifyBtn.disabled = false;

            // Clear OTP inputs on error để user có thể nhập lại
            otpInputs.forEach(input => {
                input.classList.add('error');
            });
        }
    });

    // Gửi lại mã OTP
    resendBtn.addEventListener('click', async function() {
        if (!this.disabled) {
            try {
                // Import Firebase service
                const { default: firebasePhoneAuth } = await import('{{ Vite::asset("resources/js/library/firebasePhoneAuth.js") }}');

                // Initialize reCAPTCHA
                firebasePhoneAuth.initRecaptcha('recaptcha-container');

                // Get phone from hidden input
                const phone = document.querySelector('input[name="phone"]').value;

                // ✅ Reset attempts counter trước khi gửi lại OTP
                try {
                    await fetch('/password/reset-phone-otp-attempts', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ phone: phone })
                    });
                } catch (error) {
                    console.error('Failed to reset attempts:', error);
                    // Continue anyway
                }

                // Send OTP again
                const result = await firebasePhoneAuth.sendOTP(phone);

                if (result.success) {
                    // Clear OTP inputs
                    otpInputs.forEach(input => {
                        input.value = '';
                        input.classList.remove('filled', 'error');
                    });
                    hiddenOtp.value = '';

                    // Reset OTP timer
                    otpTimeLeft = 2 * 60;
                    const otpTimerElement = document.getElementById('otp-timer');
                    if (otpTimerElement) {
                        otpTimerElement.textContent = '2:00';
                        otpTimerElement.style.color = '';
                    }

                    // Show success message
                    showMessage('Mã OTP đã được gửi lại. Bạn có 3 lần thử mới.', 'success');
                    startCountdown();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Resend error:', error);
                showMessage('Có lỗi xảy ra, vui lòng thử lại', 'error');
            }
        }
    });

    // Đếm ngược thời gian 1 phút
    function startCountdown() {
        resendBtn.disabled = true;
        resendText.classList.add('d-none');
        countdownText.classList.remove('d-none');

        countdownTimer = setInterval(() => {
            countdownValue--;
            countdown.textContent = countdownValue;

            if (countdownValue <= 0) {
                clearInterval(countdownTimer);
                resendBtn.disabled = false;
                resendText.classList.remove('d-none');
                countdownText.classList.add('d-none');
                countdownValue = 60;
            }
        }, 1000);
    }

    // Đếm ngược thời gian OTP 2 phút
    function startOTPTimer() {
        const otpTimerElement = document.getElementById('otp-timer');
        if (!otpTimerElement) return;

        otpTimer = setInterval(() => {
            otpTimeLeft--;
            const minutes = Math.floor(otpTimeLeft / 60);
            const seconds = otpTimeLeft % 60;
            otpTimerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (otpTimeLeft <= 0) {
                clearInterval(otpTimer);
                otpTimerElement.textContent = '0:00';
                otpTimerElement.style.color = '#dc2626';
                showMessage('Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.', 'error');
            }
        }, 1000);
    }

    // Xóa lỗi khi focus vào input
    otpInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('error');
        });
    });

    // Helper function to refresh CSRF token
    async function refreshCSRFToken() {
        try {
            const response = await fetch('/sanctum/csrf-cookie', {
                method: 'GET',
                credentials: 'same-origin'
            });

            if (response.ok) {
                // Get new CSRF token from meta tag
                const newToken = document.querySelector('meta[name="csrf-token"]').content;
                // CSRF token refreshed
                return newToken;
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
    }

    // Helper function to show messages
    function showMessage(message, type) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'error' ? 'danger' : 'success'} mb-3`;
        alert.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>${message}`;

        // Insert before form
        form.parentNode.insertBefore(alert, form);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 5000);
    }
});
</script>

<!-- reCAPTCHA Container -->
<div id="recaptcha-container"></div>
@endsection
