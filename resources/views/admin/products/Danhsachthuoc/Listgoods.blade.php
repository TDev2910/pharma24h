@extends('layouts.admin')

@section('title', 'Danh Sách Hàng Hóa')

@section('content')

<style>
    /* Modern UI Variables */
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* Page Layout */
    .goods-page {
        padding: 24px;
        background: var(--bg-light);
        min-height: 100vh;
    }

    /* Header Control Bar */
    .header-control-bar {
        background: white;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .controls-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    /* Title Section */
    .title-section h4 {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 20px;
        margin: 0;
        white-space: nowrap;
    }

    /* Search Section - Centered */
    .search-wrapper {
        flex: 1;
        max-width: 500px;
    }

    .search-wrapper .input-group {
        position: relative;
    }

    .search-wrapper .input-group-text {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        z-index: 10;
        color: var(--text-secondary);
        padding: 0;
        font-size: 14px;
    }

    .search-wrapper .form-control {
        padding-left: 42px;
        padding-right: 16px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        height: 40px;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .search-wrapper .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .search-wrapper .form-control::placeholder {
        color: var(--text-secondary);
    }

    /* Utility Options */
    .ultility-options {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-export {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        background: white;
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-export:hover {
        background: var(--bg-light);
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-1px);
    }

    .utility-icons {
        display: flex;
        gap: 4px;
    }

    .utility-icons .btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1.5px solid var(--border-color);
        background: white;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        padding: 0;
        font-size: 14px;
    }

    .utility-icons .btn:hover {
        background: var(--bg-light);
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-1px);
    }

    /* Table Container */
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    /* Modern Table */
    .table {
        margin-bottom: 0;
    }

    .table thead.custom-header {
        background: linear-gradient(to right, #f8fafc, #f1f5f9);
        border-bottom: 2px solid var(--border-color);
    }

    .table thead.custom-header th {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 16px 20px;
        border: none;
        text-align: center;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
        background: #fafbfc;
        transform: translateX(2px);
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }

    .table tbody td {
        padding: 16px 20px;
        color: var(--text-primary);
        font-size: 14px;
        vertical-align: middle;
        border: none;
        text-align: center;
    }

    /* Badge Styles */
    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .bg-success {
        background-color: var(--success-color) !important;
        color: white !important;
    }

    .bg-warning {
        background-color: var(--warning-color) !important;
        color: white !important;
    }

    .bg-danger {
        background-color: var(--danger-color) !important;
        color: white !important;
    }

    .bg-secondary {
        background-color: var(--secondary-color) !important;
        color: white !important;
    }

    /* Price Styling */
    .price-cell {
        font-weight: 600;
        color: var(--primary-color);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        color: var(--text-secondary);
        opacity: 0.5;
        margin-bottom: 16px;
    }

    .empty-state p {
        color: var(--text-secondary);
        font-size: 15px;
        margin: 0;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 20px 24px;
        border-top: 1px solid var(--border-color);
    }

    .pagination {
        margin: 0;
        gap: 4px;
    }

    .page-link {
        border: 1.5px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        padding: 8px 14px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        margin: 0 2px;
    }

    .page-link:hover {
        background: var(--bg-light);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .page-item.active .page-link {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .goods-page {
            padding: 16px;
        }

        .top-row {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .ultility-options {
            justify-content: space-between;
        }

        .search-wrapper {
            max-width: 100%;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            min-width: 1000px;
        }
    }

    @media (max-width: 576px) {
        .header-control-bar {
            padding: 16px;
        }

        .title-section h4 {
            font-size: 18px;
        }

        .btn-export span {
            display: none;
        }

        .btn-export {
            padding: 10px 14px;
        }
    }

    /* Scrollbar Styling */
    .table-container::-webkit-scrollbar {
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: var(--bg-light);
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: var(--text-secondary);
    }
</style>

<div class="goods-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="controls-section">
            <!-- Title Section -->
            <div class="title-section">
                <h4>Danh sách hàng hóa</h4>
            </div>

            <!-- Search Section -->
            <div class="search-wrapper">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Tìm kiếm theo mã, tên hàng hóa...">
                </div>
            </div>
            
            <!-- Utility Options -->
            <div class="ultility-options">
                <!-- Xuất file -->
                <button class="btn-export">
                    <i class="fas fa-upload"></i>
                    <span>Xuất file</span>
                </button>
                
                <!-- Utility Icons -->
                <div class="utility-icons">
                    <button class="btn" title="Chế độ xem">
                        <i class="fas fa-list"></i>
                    </button>
                    <button class="btn" title="Cài đặt">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button class="btn" title="Trợ giúp">
                        <i class="fas fa-question-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table class="table table-hover align-middle">
            <thead class="custom-header">
                <tr>
                    <th>Mã hàng</th>
                    <th>Tên hàng hóa</th>
                    <th>Nhóm hàng</th>
                    <th>Quy cách đóng gói</th>
                    <th>ĐVT</th>
                    <th>Giá vốn</th>
                    <th>Giá bán</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($goods as $good)
                    <tr>
                        <td><strong>{{ $good->ma_hang ?? 'N/A' }}</strong></td>
                        <td>{{ $good->ten_hang_hoa ?? 'N/A' }}</td>
                        <td>{{ $good->category->name ?? 'N/A' }}</td>
                        <td>{{ $good->quy_cach_dong_goi ?? 'N/A' }}</td>
                        <td>{{ $good->don_vi_tinh ?? 'N/A' }}</td>
                        <td class="price-cell">{{ $good->gia_von_formatted ?? '0 VND' }}</td>
                        <td class="price-cell">{{ $good->gia_ban_formatted ?? '0 VND' }}</td>
                        <td>
                            @if($good->ban_truc_tiep)
                                <span class="badge bg-success">Bán trực tiếp</span>
                            @else
                                <span class="badge bg-secondary">Không bán</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x"></i>
                                <p>Không có hàng hóa nào được tìm thấy</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($goods->hasPages())
            <div class="pagination-wrapper d-flex justify-content-center">
                {{ $goods->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@endsection