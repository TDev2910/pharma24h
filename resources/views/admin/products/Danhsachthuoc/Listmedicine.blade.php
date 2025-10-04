@extends('layouts.admin')

@section('title', 'Danh Sách Thuốc')

@push('styles')
<style>
    /* Modern UI Variables */
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* Page Layout */
    .medicine-page {
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
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    /* Title Section */
    .title-section h4 {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 20px;
        margin: 0;
        white-space: nowrap;
    }

    /* Search Box */
    .search-wrapper {
        flex: 1;
        max-width: 465px;
        min-width: 280px;
    }

    .search-wrapper .input-group {
        position: relative;
    }

    .search-wrapper .input-group-text {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        z-index: 10;
        color: var(--text-secondary);
        padding: 0;
    }

    .search-wrapper .form-control {
        padding-left: 40px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        height: 42px;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .search-wrapper .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* Utility Options */
    .ultility-options {
        display: flex;
        gap: 8px;
        margin-left: auto;
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
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-export:hover {
        background: var(--bg-light);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .utility-icons {
        display: flex;
        gap: 4px;
    }

    .utility-icons .btn {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1.5px solid var(--border-color);
        background: white;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        padding: 0;
    }

    .utility-icons .btn:hover {
        background: var(--bg-light);
        border-color: var(--primary-color);
        color: var(--primary-color);
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
        white-space: nowrap;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
        background: #fafbfc;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
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
    }

    /* Badge/Tag Styles for Active Ingredients */
    .badge-ingredient {
        display: inline-block;
        padding: 4px 10px;
        background: #e0e7ff;
        color: #4338ca;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
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
    .medicine-pagination {
        padding: 20px 24px;
        border-top: 1px solid var(--border-color);
    }

    .medicine-pagination .pagination {
        margin: 0;
        gap: 4px;
    }

    .medicine-pagination .page-link {
        border: 1.5px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        padding: 8px 14px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        margin: 0 2px;
    }

    .medicine-pagination .page-link:hover {
        background: var(--bg-light);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .medicine-pagination .page-item.active .page-link {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .medicine-page {
            padding: 16px;
        }

        .controls-section {
            flex-direction: column;
            align-items: stretch;
        }

        .search-wrapper {
            max-width: 100%;
        }

        .ultility-options {
            margin-left: 0;
            justify-content: space-between;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            min-width: 800px;
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
@endpush

@section('content')
<div class="medicine-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
            <!-- Title Section -->
            <div class="title-section">
                <h4>Danh sách thuốc</h4>
            </div>
            <!-- Search Section -->
            <div style="flex:1; display:flex; justify-content:center;">
                <div class="search-wrapper">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Theo mã, tên hàng" id="searchInput">
                    </div>
                </div>
            </div>
            <!-- Utility Options -->
            <div class="ultility-options">
                <!-- Xuất file -->
                <button class="btn-export">
                    <i class="fas fa-upload"></i>
                    Xuất file
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
                    <th>Mã thuốc</th>
                    <th>Tên thuốc</th>
                    <th>Hoạt chất chính</th>
                    <th>Hàm lượng</th>
                    <th>Quy cách đóng gói</th>
                    <th>ĐVT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicines as $medicine)
                    <tr>
                        <td><strong>{{ $medicine->ma_hang ?? 'N/A' }}</strong></td>
                        <td>{{ $medicine->ten_thuoc ?? 'N/A' }}</td>
                        <td>
                            <span class="badge-ingredient">{{ $medicine->hoat_chat ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $medicine->ham_luong ?? 'N/A' }}</td>
                        <td>{{ $medicine->quy_cach_dong_goi ?? 'N/A' }}</td>
                        <td>{{ $medicine->don_vi_tinh ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x"></i>
                                <p>Không có thuốc nào được tìm thấy</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($medicines->hasPages())
            <div class="medicine-pagination d-flex justify-content-center">
                {{ $medicines->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection