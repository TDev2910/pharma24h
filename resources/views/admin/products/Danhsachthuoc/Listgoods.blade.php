@extends('layouts.admin')

@section('title', 'Danh Sách Hàng Hóa')

@section('content')

    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <!-- Controls Section -->
        <div class="controls-section">

            <!-- Title Section -->
            <div class="title-section d-flex align-items-center">
                <h4>Danh sách hàng hóa</h4>
            </div>

            <!-- Search Section -->
            <div style="width: 465px;">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Theo mã, tên hàng">
                </div>
            </div>
                 
                <div class="ultility-options d-flex align-items-center">
                    <!-- Xuất file -->
                    <button class="btn btn-outline-secondary me-2">
                        <i class="fas fa-upload me-1"></i>
                        Xuất file
                    </button>
                    
                    <!-- Utility Icons -->
                    <div class="utility-icons d-flex align-items-center">
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
        <div class="table-container">
            <table class="table table-bordered table-hover align-middle">
                <thead class="custom-header">
                    <tr>
                        <th>Mã hàng</th>
                        <th>Tên hàng hóa</th>
                        <th>Nhóm hàng</th>
                        <th>Quy cách đóng gói</th>
                        <th>ĐVT</th>
                        <th>Tồn kho</th>
                        <th>Giá vốn</th>
                        <th>Giá bán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($goods as $good)
                        <tr>
                            <td>{{ $good->ma_hang ?? 'N/A' }}</td>
                            <td>{{ $good->ten_hang_hoa ?? 'N/A' }}</td>
                            <td>{{ $good->category->name ?? 'N/A' }}</td>
                            <td>{{ $good->quy_cach_dong_goi ?? 'N/A' }}</td>
                            <td>{{ $good->don_vi_tinh ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $good->ton_kho <= $good->ton_thap_nhat ? 'bg-danger' : ($good->ton_kho >= $good->ton_cao_nhat ? 'bg-warning' : 'bg-success') }}">
                                    {{ $good->ton_kho ?? 0 }}
                                </span>
                            </td>
                            <td>{{ number_format($good->gia_von) }} VNĐ</td>
                            <td>{{ number_format($good->gia_ban) }} VNĐ</td>
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
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Không có hàng hóa nào được tìm thấy</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Pagination -->
            @if($goods->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $goods->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

<style>
    .header-control-bar {
        padding: 1em;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 16px;
    }

    .controls-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .title-section h4 {
        margin: 0;
        color: #333;
        font-weight: 600;
    }

    .ultility-options {
        gap: 0.5rem;
    }

    .utility-icons {
        gap: 0.25rem;
    }

    .utility-icons .btn {
        padding: 0.5rem;
        border: none;
        background: transparent;
        color: #666;
        transition: color 0.2s;
    }

    .utility-icons .btn:hover {
        color: #333;
    }

    .table-container {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .custom-header {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .custom-header th {
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 600;
        color: #495057;
        text-align: center;
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table tbody td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    .bg-secondary {
        background-color: #6c757d !important;
    }
</style>
