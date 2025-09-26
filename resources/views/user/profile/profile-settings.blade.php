@extends('layouts.user')

@section('title', 'Cài đặt hồ sơ')

@section('page-title', 'Cài đặt hồ sơ')
@section('page-description', 'Quản lý thông tin cá nhân và tùy chọn tài khoản của bạn')

@push('styles')
<style>
    /* Settings Content */
    .settings-content {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 40px;
        max-width: 1200px;
    }

    /* Profile Section */
    .profile-section {
        background: white;
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        height: fit-content;
    }

    .profile-photo {
        text-align: center;
        margin-bottom: 30px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: white;
        position: relative;
    }

    .profile-photo h5 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .profile-photo p {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .photo-buttons {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .btn-upload {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-upload:hover {
        background: #2563eb;
    }

    .btn-remove {
        background: transparent;
        color: #ef4444;
        border: none;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-remove:hover {
        background: #fef2f2;
        border-radius: 8px;
    }

    /* Loading states */
    .uploading {
        opacity: 0.7;
        pointer-events: none;
    }

    /* Form Section */
    .form-section {
        background: white;
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #e2e8f0;
    }

    .section-title {
        color: #1e293b;
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        color: #374151;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* Address Details */
    .address-details {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        margin-top: 8px;
    }

    .address-select {
        background: white;
    }

    .address-select:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-secondary {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary:hover {
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .alert-success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .settings-content {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Settings Content -->
<div class="settings-content">
    <!-- Profile Photo Section -->
    <div class="profile-section">
        <div class="profile-photo">
            <div class="profile-avatar" id="profileAvatar">
                @if($user->avatar ?? false)
                    <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <h5>Ảnh đại diện</h5>
            <p>Ảnh đại diện sẽ được hiển thị trên hồ sơ của bạn</p>
            <div class="photo-buttons">
                <button class="btn-upload" onclick="document.getElementById('avatarInput').click()">
                    <i class="fas fa-camera me-1"></i>Tải lên ảnh mới
                </button>
                <button class="btn-remove" onclick="removeAvatar()">Xóa</button>
            </div>
            <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="handleFileSelect(this)">
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h3 class="section-title">Thông tin cá nhân</h3>                   
        <form method="POST" action="{{ route('user.profile.settings.update') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" 
                       value="{{ old('email', $user->email) }}" required>
            </div>
            
            <div class="form-row">
               <div class="form-group">
                   <label class="form-label">Số điện thoại</label>
                   <input type="text" name="phone" class="form-control" 
                          value="{{ old('phone', $user->phone ?? '') }}" placeholder="+84 xxx xxx xxx">
               </div>
               <div class="form-group">
                   <label class="form-label">Vai trò</label>
                   <input type="text" class="form-control" value="{{ ucfirst($user->role ?? 'user') }}" readonly>
               </div>
           </div>

           <div class="form-group">
               <label class="form-label">Địa chỉ</label>
               <input type="text" name="address" class="form-control" 
                      value="{{ old('address', $user->address ?? '') }}" placeholder="Địa chỉ của bạn">
           </div>

           <!-- Address Details Section -->
           <div class="form-group">
               <label class="form-label">Địa chỉ chi tiết (tùy chọn)</label>
               <div class="address-details">
                   <div class="form-row">
                       <div class="form-group">
                           <label class="form-label">Tỉnh/Thành phố</label>
                           <select id="province" name="province" class="form-control address-select">
                               <option value="">-- Chọn tỉnh/thành phố --</option>
                           </select>
                       </div>
                       <div class="form-group">
                           <label class="form-label">Quận/Huyện</label>
                           <select id="district" name="district" class="form-control address-select" disabled>
                               <option value="">-- Chọn quận/huyện --</option>
                           </select>
                       </div>
                   </div>
                   <div class="form-group">
                       <label class="form-label">Xã/Phường</label>
                       <select id="ward" name="ward" class="form-control address-select" disabled>
                           <option value="">-- Chọn xã/phường --</option>
                       </select>
                   </div>
               </div>
           </div>

            <div style="margin-top: 32px;">
                <h3 class="section-title">Thay đổi mật khẩu</h3>
                
                <div class="form-row">
                   <div class="form-group">
                       <label class="form-label">Mật khẩu hiện tại</label>
                       <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại">
                   </div>
                   <div class="form-group">
                       <label class="form-label">Mật khẩu mới</label>
                       <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới">
                   </div>
               </div>
               
               <div class="form-group">
                   <label class="form-label">Xác nhận mật khẩu mới</label>
                   <input type="password" name="new_password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
               </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('user.dashboard') }}" class="btn-secondary">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn-primary">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===== AVATAR UPLOAD MANAGEMENT =====
    function handleFileSelect(input) {
        const file = input.files[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                showAlert('Kích thước file phải nhỏ hơn 10MB', 'error');
                return;
            }
            
            if (!file.type.startsWith('image/')) {
                showAlert('Vui lòng chọn file hình ảnh', 'error');
                return;
            }
            
            uploadAvatar(file);
        }
    }

    function uploadAvatar(file) {
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', '{{ csrf_token() }}');
        
        const avatar = document.getElementById('profileAvatar');
        avatar.classList.add('uploading');
        
        fetch('{{ route("user.upload.avatar") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update avatar display
                avatar.innerHTML = `<img src="${data.avatar_url}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">`;
                showAlert('Cập nhật ảnh đại diện thành công!', 'success');
            } else {
                showAlert(data.message || 'Tải ảnh lên thất bại', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Tải ảnh lên thất bại', 'error');
        })
        .finally(() => {
            avatar.classList.remove('uploading');
        });
    }

    function removeAvatar() {
        if (!confirm('Bạn có chắc chắn muốn xóa ảnh đại diện?')) {
            return;
        }
        
        fetch('{{ route("user.remove.avatar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('profileAvatar').innerHTML = '<i class="fas fa-user"></i>';
                showAlert('Xóa ảnh đại diện thành công!', 'success');
            } else {
                showAlert(data.message || 'Xóa ảnh thất bại', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Xóa ảnh thất bại', 'error');
        });
    }

    // Alert function
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
        
        const formSection = document.querySelector('.form-section');
        formSection.insertBefore(alertDiv, formSection.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // ===== ADDRESS API MANAGEMENT =====
    document.addEventListener('DOMContentLoaded', function() {
        loadProvinces();
        loadCurrentAddress();
    });

    function loadCurrentAddress() {
        const provinceCode = '{{ old("province", $user->province ?? "") }}';
        const districtCode = '{{ old("district", $user->district ?? "") }}';
        const wardCode = '{{ old("ward", $user->ward ?? "") }}';

        if (provinceCode) {
            loadDistricts(provinceCode, districtCode);
        }
        if (districtCode) {
            loadWards(districtCode, wardCode);
        }
    }

    function loadProvinces() {
        fetch('https://provinces.open-api.vn/api/?depth=1')
            .then(response => response.json())
            .then(data => {
                const provinceSelect = document.getElementById('province');
                provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
                
                data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.code;
                    option.textContent = province.name;
                    provinceSelect.appendChild(option);
                });

                // Set current province if exists
                const currentProvince = '{{ old("province", $user->province ?? "") }}';
                if (currentProvince) {
                    provinceSelect.value = currentProvince;
                }
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
                document.getElementById('province').innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    }

    function loadDistricts(provinceCode, selectedDistrict = '') {
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');

        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
        wardSelect.disabled = true;

        if (!provinceCode) {
            districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                districtSelect.disabled = false;
                districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
                
                data.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.code;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });

                // Set current district if exists
                if (selectedDistrict) {
                    districtSelect.value = selectedDistrict;
                }
            })
            .catch(error => {
                console.error('Error loading districts:', error);
                districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                districtSelect.disabled = false;
            });
    }

    function loadWards(districtCode, selectedWard = '') {
        const wardSelect = document.getElementById('ward');

        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.disabled = true;

        if (!districtCode) {
            wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                wardSelect.disabled = false;
                wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
                
                data.wards.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.code;
                    option.textContent = ward.name;
                    wardSelect.appendChild(option);
                });

                // Set current ward if exists
                if (selectedWard) {
                    wardSelect.value = selectedWard;
                }
            })
            .catch(error => {
                console.error('Error loading wards:', error);
                wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                wardSelect.disabled = false;
            });
    }

    // Event listeners for address selects
    document.getElementById('province').addEventListener('change', function() {
        loadDistricts(this.value);
    });

    document.getElementById('district').addEventListener('change', function() {
        loadWards(this.value);
    });
</script>
@endpush