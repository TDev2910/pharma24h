<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Settings - Sức Khỏe 24h</title>
    
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
                <h3><i class="fas fa-heartbeat me-2"></i>Sức Khỏe 24h</h3>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <h5>{{ $user->name ?? 'User' }}</h5>
                    <small>{{ $user->email ?? 'user@example.com' }}</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li><a href="{{ route('user.dashboard') }}" class="active"><i class="fas fa-user-cog"></i>Account Settings</a></li>
                <li><a href="{{ route('user.cart') }}"><i class="fas fa-shopping-cart"></i>Giỏ hàng</a></li>
                <li><a href="{{ route('user.orders') }}"><i class="fas fa-clipboard-list"></i>Đơn hàng</a></li>
                <li><a href="#"><i class="fas fa-calendar-check"></i>Lịch khám</a></li>
                <li><a href="#"><i class="fas fa-file-medical"></i>Hồ sơ sức khỏe</a></li>
                <li><a href="#"><i class="fas fa-bell"></i>Thông báo</a></li>
                <li><a href="{{ route('home') }}"><i class="fas fa-home"></i>Trang chủ</a></li>
                <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i>Đăng xuất</a></li>
            </ul>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="settings-header">
                <h1>Account Settings</h1>
                <p>Manage your account settings and preferences</p>
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
                        <h5>Your Photo</h5>
                        <p>This will be displayed on your profile</p>
                        <div class="photo-buttons">
                            <button class="btn-upload" onclick="document.getElementById('avatarInput').click()">
                                <i class="fas fa-camera me-1"></i>Upload New
                            </button>
                            <button class="btn-remove" onclick="removeAvatar()">Remove</button>
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

                    <h3 class="section-title">Personal Information</h3>
                    
                                               <form method="POST" action="{{ route('user.dashboard.update') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email', $user->email) }}" required>
                        </div>
                        
                                                       <div class="form-row">
                                   <div class="form-group">
                                       <label class="form-label">Mobile Number</label>
                                       <input type="text" name="phone" class="form-control" 
                                              value="{{ old('phone', $user->phone ?? '') }}" placeholder="+84 xxx xxx xxx">
                                   </div>
                                   <div class="form-group">
                                       <label class="form-label">Role</label>
                                       <input type="text" class="form-control" value="{{ ucfirst($user->role ?? 'user') }}" readonly>
                                   </div>
                               </div>

                               <div class="form-group">
                                   <label class="form-label">Address</label>
                                   <input type="text" name="address" class="form-control" 
                                          value="{{ old('address', $user->address ?? '') }}" placeholder="Địa chỉ của bạn">
                               </div>

                        <div style="margin-top: 32px;">
                            <h3 class="section-title">Change Password</h3>
                            
                                                               <div class="form-row">
                                       <div class="form-group">
                                           <label class="form-label">Current Password</label>
                                           <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại">
                                       </div>
                                       <div class="form-group">
                                           <label class="form-label">New Password</label>
                                           <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới">
                                       </div>
                                   </div>
                                   
                                   <div class="form-group">
                                       <label class="form-label">Confirm New Password</label>
                                       <input type="password" name="new_password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
                                   </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('home') }}" class="btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary">
                                Save Changes
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
        // Avatar Upload
        function handleFileSelect(input) {
            const file = input.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    alert('File size must be less than 10MB');
                    return;
                }
                
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
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
                    showAlert('Avatar updated successfully!', 'success');
                } else {
                    showAlert(data.message || 'Upload failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Upload failed', 'error');
            })
            .finally(() => {
                avatar.classList.remove('uploading');
            });
        }

        function removeAvatar() {
            if (!confirm('Are you sure you want to remove your avatar?')) {
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
                    showAlert('Avatar removed successfully!', 'success');
                } else {
                    showAlert(data.message || 'Remove failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Remove failed', 'error');
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
