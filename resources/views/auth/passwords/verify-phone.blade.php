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
            Chúng tôi đã gửi mã OTP đến:<br>
            <strong class="phone-highlight">{{ $phone }}</strong>
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
    
    // Focus first input
    otpInputs[0].focus();
    
    // Start countdown
    startCountdown();
    
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
                    form.submit();
                }
            }, 500);
        }
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
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
        
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác thực...';
        verifyBtn.disabled = true;
    });
    
    // Resend functionality
    resendBtn.addEventListener('click', async function() {
        if (!this.disabled) {
            try {
                // Import Firebase service
                const { default: firebasePhoneAuth } = await import('{{ Vite::asset("resources/js/services/firebasePhoneAuth.js") }}');
                
                // Initialize reCAPTCHA
                firebasePhoneAuth.initRecaptcha('recaptcha-container');
                
                // Get phone from hidden input
                const phone = document.querySelector('input[name="phone"]').value;
                
                // Send OTP again
                const result = await firebasePhoneAuth.sendOTP(phone);
                
                if (result.success) {
                    // Show success message
                    showMessage('Mã OTP đã được gửi lại', 'success');
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
    
    // Countdown functionality
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
    
    // Clear errors on focus
    otpInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('error');
        });
    });
    
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
