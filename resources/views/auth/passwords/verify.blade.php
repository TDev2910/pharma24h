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

        <!-- OTP Form -->
        <form method="POST" action="{{ route('password.verify.post') }}" id="otpForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="otp" id="hiddenOtp">

            <!-- 5 OTP Input Boxes -->
            <div class="otp-inputs-container mb-4">
                <input type="text" class="otp-input" maxlength="1" data-index="0" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="1" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="2" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="3" autocomplete="off">
                <input type="text" class="otp-input" maxlength="1" data-index="4" autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-4" id="verifyBtn">
                Xác thực mã OTP
            </button>
        </form>

        <!-- Resend Section -->
        <div class="text-center">
            <p class="footer-text">
                Chưa nhận được mã OTP? 
                <form method="POST" action="{{ route('password.email') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="resend-link">Gửi lại mã OTP</button>
                </form>
            </p>
            <p class="footer-text">
                <a href="{{ route('password.request') }}" class="footer-link">Quay lại email</a>
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

.email-highlight {
    color: #667eea;
    font-weight: 600;
}

/* OTP Inputs Container */
.otp-inputs-container {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 32px;
}

/* Individual OTP Input */
.otp-input {
    width: 60px;
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

.resend-link:hover {
    color: #5a67d8;
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
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .otp-inputs-container {
        gap: 8px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.otp-input');
    const form = document.getElementById('otpForm');
    const hiddenOtp = document.getElementById('hiddenOtp');
    const verifyBtn = document.getElementById('verifyBtn');
    
    // Focus first input
    otpInputs[0].focus();
    
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
            const numbers = paste.replace(/\D/g, '').slice(0, 5);
            
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
        
        // Auto submit when 5 digits entered
        if (otp.length === 5) {
            setTimeout(() => {
                if (hiddenOtp.value.length === 5) {
                    verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
                    verifyBtn.disabled = true;
                    form.submit();
                }
            }, 500);
        }
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const otp = hiddenOtp.value;
        
        if (otp.length !== 5) {
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
        
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
        verifyBtn.disabled = true;
    });
    
    // Clear errors on focus
    otpInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('error');
        });
    });
});
</script>
@endsection
