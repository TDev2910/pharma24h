@extends('layouts.admin')

@section('title', 'Danh Sách Thuốc')

@section('content')

    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <!-- Controls Section -->
        <div class="controls-section">

            <!-- Title Section -->
            <div class="title-section d-flex align-items-center">
                <h4>Danh sách thuốc</h4>
            </div>

            <!-- Search Section -->
            <div style="width: 465px;">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Theo mã, tên hàng">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-filter"></i>
                    </button>
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
                            <td>{{ $medicine->ma_hang ?? 'N/A' }}</td>
                            <td>{{ $medicine->ten_thuoc ?? 'N/A' }}</td>
                            <td>{{ $medicine->hoat_chat ?? 'N/A' }}</td>
                            <td>{{ $medicine->ham_luong ?? 'N/A' }}</td>
                            <td>{{ $medicine->quy_cach_dong_goi ?? 'N/A' }}</td>
                            <td>{{ $medicine->don_vi_tinh ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Không có thuốc nào được tìm thấy</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Pagination -->
            @if($medicines->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $medicines->appends(request()->query())->links() }}
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
    .title-section {
        margin-top: -5px;

    }   
    .title-section >h4{
        margin-bottom: 0;
    }
    .nav-right{
        margin-right: 450px;
    }
    /* CSS để căn chỉnh utility icons ngang hàng với các nút khác */
    .controls-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    
    .utility-icons {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .utility-icons .btn {
        height: 38px;
        padding: 7px 10px;
        font-size: 14px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        /* Màu sắc giống nút Xuất file */
        background-color: #fff;
        border: 1px solid #6c757d;
        color: #6c757d;
        transition: all 0.2s ease;
    }
    
    .utility-icons .btn:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }
    
    .utility-icons .btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
        outline: none;
    }
    
    .utility-icons .btn i {
        font-size: 14px;
    }
    
    /* Đảm bảo tất cả buttons trong controls-section có cùng chiều cao */
    .controls-section .btn {
        height: 38px;
        display: flex;
        align-items: center;
    }
    
    /* Điều chỉnh search container */
    .search-container .input-group {
        height: 38px;
    }
    
    .search-container .input-group .form-control {
        height: 38px;
    }
    
    .search-container .input-group .btn {
        height: 38px;
    }
    
    /* CSS cho table container - TOÀN BỘ */
    .table-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 20px;
        margin-top: 16px;
        width: 100%;
    }
    
    /* CSS cho table - TOÀN BỘ */
    .table {
        width: 100%;
        margin-bottom: 0;
    }
    
    /* CSS cho các cột table */
    .table th {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        font-weight: 600;
        padding: 12px 8px;
        vertical-align: middle;
    }
    
    /* CSS cho custom header với độ ưu tiên cao */
    .custom-header th {
        background-color: #A2D5C6 !important;
        color: white !important;
        border-color: #A2D5C6 !important;
        font-weight: 600;
        padding: 12px 8px;
        vertical-align: middle;
    }
    
    .table td {
        padding: 12px 8px;
        vertical-align: middle;
        border-color: #dee2e6;
    }
    
    /* CSS cho cột thao tác */
    .table td:last-child {
        width: 120px;
        text-align: center;
    }
    
    /* CSS cho button group trong table */
    .btn-group-sm .btn {
        padding: 4px 8px;
        font-size: 12px;
    }
    
    /* Responsive cho table */
    @media (max-width: 768px) {
        .table-container {
            padding: 10px;
        }
        
        .table th,
        .table td {
            padding: 8px 4px;
            font-size: 14px;
        }
    }
    .table-container {
        margin-top: -30px;
    }
</style>

<script>
function deleteMedicine(id) {
    if (confirm('Bạn có chắc chắn muốn xóa thuốc này?')) {
        // Tạo form để gửi request DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/medicines/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>