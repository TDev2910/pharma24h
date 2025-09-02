@extends('layouts.user')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Welcome back! Here\'s your account overview')

@push('styles')
<style>
    /* Dashboard Content */
    .dashboard-content {
        max-width: 1200px;
    }

    /* Welcome Section */
    .welcome-section {
        margin-bottom: 40px;
    }

    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 40px;
        color: white;
        text-align: center;
    }

    .welcome-text h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .welcome-text p {
        font-size: 16px;
        opacity: 0.9;
        margin: 0;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 20px;
    }

    .stat-info h3 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 4px 0;
    }

    .stat-info p {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    /* Quick Actions */
    .quick-actions {
        background: white;
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #e2e8f0;
    }

    .quick-actions h3 {
        color: #1e293b;
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 20px;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        text-decoration: none;
        color: #374151;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: #f1f5f9;
        color: #3b82f6;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .action-btn i {
        font-size: 18px;
        width: 20px;
    }

    .action-btn span {
        font-weight: 500;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-card">
                <div class="welcome-text">
                    <h2>Xin chào, {{ $user->name }}! 👋</h2>
                    <p>Chào mừng bạn quay trở lại với Sức Khỏe 24h</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-info">
                    <h3>0</h3>
                    <p>Đơn hàng</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-info">
                    <h3>0</h3>
                    <p>Thông báo</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="stat-info">
                    <h3>1</h3>
                    <p>Hồ sơ sức khỏe</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>Thao tác nhanh</h3>
            <div class="action-buttons">
                <a href="{{ route('user.profile.settings') }}" class="action-btn">
                    <i class="fas fa-user-cog"></i>
                    <span>Cài đặt tài khoản</span>
                </a>
                <a href="{{ route('user.orders') }}" class="action-btn">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Xem đơn hàng</span>
                </a>
                <a href="{{ route('user.health.profile') }}" class="action-btn">
                    <i class="fas fa-file-medical"></i>
                    <span>Hồ sơ sức khỏe</span>
                </a>
            </div>
        </div>
    </div>
@endsection