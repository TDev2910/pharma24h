@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/auth/register.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center register-container">
    <div class="row justify-content-center w-100">
        <div class="col-11 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg border-0 register-card auth-card">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="register-header auth-header">
                        <h2 class="fw-bold mb-2">Register</h2>
                        <p class="text-muted small">Tạo tài khoản để bắt đầu sử dụng<br>Vui lòng điền vào tất cả các thông tin cần thiết</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
                        @csrf       
                        <!-- Name Input -->
                        <div class="mb-3">
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Họ và tên"
                                   required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Email-->
                        <div class="mb-3 position-relative">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Địa chỉ email"
                                   required>
                            
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="position-relative">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Mật khẩu"
                                           required>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Xác nhận mật khẩu"
                                       required>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address') }}" 
                                   placeholder="Địa chỉ cụ thể"
                                   required>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Province -->
                        <div class="mb-3">
                            <select id="province" 
                                    name="province" 
                                    class="form-select @error('province') is-invalid @enderror" 
                                    required>
                                <option value="">Đang tải tỉnh thành...</option>
                            </select>
                            @error('province')
                                <span class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- District -->
                        <div class="mb-3">
                            <select id="district" 
                                    name="district" 
                                    class="form-select @error('district') is-invalid @enderror" 
                                    required 
                                    disabled>
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                            @error('district')
                                <span class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Ward -->
                        <div class="mb-3">
                            <select id="ward" 
                                    name="ward" 
                                    class="form-select @error('ward') is-invalid @enderror" 
                                    required 
                                    disabled>
                                <option value="">-- Chọn Phường/Xã --</option>
                            </select>
                            @error('ward')
                                <span class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Register Button -->
                        <div class="mb-4">
                            <button type="submit" 
                                    class="btn w-100 fw-medium btn-register">
                                Tạo tài khoản
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <span class="text-muted small">Đã có tài khoản? </span>
                            <a href="{{ route('login') }}" class="text-decoration-none fw-medium">Đăng nhập ngay</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Show selected file name
function showFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = `Đã chọn: ${input.files[0].name}`;
        fileName.style.color = '#28a745';
    } else {
        fileName.textContent = '';
    }
}

// Province/District/Ward API using provinces.open-api.vn
document.addEventListener('DOMContentLoaded', function() {
    // Load provinces
    fetch('https://provinces.open-api.vn/api/?depth=1')  
        .then(response => response.json())
        .then(data => {
            const provinceSelect = document.getElementById('province');
            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
            
            data.forEach(province => {
                provinceSelect.insertAdjacentHTML('beforeend',
                    `<option value="${province.code}">${province.name}</option>`);
            });
        })
        .catch(error => {
            console.error('Lỗi tải danh sách tỉnh:', error);
            document.getElementById('province').innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        });

    // Province change - load districts
    document.getElementById('province').addEventListener('change', function(){
        const provinceCode = this.value;
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');

        districtSelect.innerHTML = '<option value="">Đang tải...</option>'; 
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>'; 
        wardSelect.disabled = true;

        if (!provinceCode) {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                districtSelect.disabled = false;
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                
                data.districts.forEach(district => {
                    districtSelect.insertAdjacentHTML('beforeend',
                        `<option value="${district.code}">${district.name}</option>`);
                });
            })
            .catch(error => {
                console.error('Lỗi tải danh sách quận/huyện:', error);
                districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                districtSelect.disabled = false;
            });
    });

    // District change - load wards
    document.getElementById('district').addEventListener('change', function(){
        const districtCode = this.value;
        const wardSelect = document.getElementById('ward');

        wardSelect.innerHTML = '<option value="">Đang tải...</option>'; 
        wardSelect.disabled = true;

        if (!districtCode) {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                wardSelect.disabled = false;
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                
                data.wards.forEach(ward => {
                    wardSelect.insertAdjacentHTML('beforeend',
                        `<option value="${ward.code}">${ward.name}</option>`);
                });
            })
            .catch(error => {
                console.error('Lỗi tải danh sách phường/xã:', error);
                wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                wardSelect.disabled = false;
            });
    });

    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePassword() {
        if (password.value && passwordConfirmation.value) {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
                passwordConfirmation.classList.add('is-invalid');
            } else {
                passwordConfirmation.setCustomValidity('');
                passwordConfirmation.classList.remove('is-invalid');
            }
        }
    }
    
    password.addEventListener('input', validatePassword);
    passwordConfirmation.addEventListener('input', validatePassword);
});
</script>
@endpush
@endsection