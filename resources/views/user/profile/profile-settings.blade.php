<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile Settings - Sức Khỏe 24h</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }

        .settings-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e2e8f0;
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 30px 30px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }

        .sidebar-header h3 {
            color: #1e293b;
            font-weight: 700;
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            padding: 0 30px 20px;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
        }

        .user-details h5 {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .user-details small {
            color: #64748b;
            font-size: 12px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            margin-bottom: 2px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 30px;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #f1f5f9;
            color: #3b82f6;
        }

        .sidebar-menu a i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
        }

        .settings-header {
            margin-bottom: 40px;
        }

        .settings-header h1 {
            color: #1e293b;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 8px;
        }

        .settings-header p {
            color: #64748b;
            font-size: 16px;
            margin: 0;
        }

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
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .settings-header h1 {
                font-size: 24px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3><i style="margin-right: 25px;"></i>Pharma24h</h3>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    @if($user->avatar ?? false)
                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>
                <div class="user-details">
                    <h5>{{ $user->name ?? 'User' }}</h5>
                    <small>{{ $user->email ?? 'user@example.com' }}</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <!-- Dashboard Overview -->
                <li><a href="{{ route('user.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a></li>
                
                <!-- Account Settings - Active -->
                <li><a href="{{ route('user.profile.settings') }}" class="active">
                    <i class="fas fa-user-cog"></i>Account Settings
                </a></li>
                
                <!-- Đơn hàng của tôi -->
                <li><a href="{{ route('user.orders') }}">
                    <i class="fas fa-clipboard-list"></i>Đơn hàng
                </a></li>
                
                <!-- Hồ sơ sức khỏe -->
                <li><a href="{{ route('user.health.profile') }}">
                    <i class="fas fa-file-medical"></i>Hồ sơ sức khỏe
                </a></li>
                
                <!-- Thông báo -->
                <li><a href="{{ route('user.notifications') }}">
                    <i class="fas fa-bell"></i>Thông báo
                </a></li>
                
                <!-- Divider -->
                <li style="margin: 20px 0; border-top: 1px solid #e2e8f0;"></li>
                
                <!-- Trang chủ -->
                <li><a href="{{ route('home') }}">
                    <i class="fas fa-home"></i>Trang chủ
                </a></li>
                
                <!-- Đăng xuất -->
                <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>Đăng xuất
                </a></li>
            </ul>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="settings-header">
                <h1>Cài đặt hồ sơ</h1>
                <p>Quản lý thông tin cá nhân và tùy chọn tài khoản của bạn</p>
            </div>

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
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ===== SIDEBAR NAVIGATION MANAGEMENT =====
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation based on current URL
            setActiveNavigation();
        });

        function setActiveNavigation() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar-menu a');
            
            // Remove all active classes
            navLinks.forEach(link => link.classList.remove('active'));
            
            // Set active based on current path
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && (currentPath === href || currentPath.includes(href))) {
                    link.classList.add('active');
                }
            });
        }

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
    </script>
</body>
</html>
