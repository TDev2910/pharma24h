@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Title Section -->
            <div class="title-section">
                <h4 class="header-title mb-0">Nhà cung cấp</h4>
            </div>
            <!-- Controls Section -->
            <div class="controls-section d-flex align-items-center gap-3">
                <!-- Search Section -->
                <div class="col-lg-4 col-md-5" style="margin-right: 60px; width: 465px;">
                    <div class="search-container">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Theo mã, tên nhà cung cấp" onkeyup="searchSuppliers()">
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="action-buttons d-flex align-items-center gap-2">
                    <!-- Tạo mới -->
                    <button class="btn btn-outline-secondary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                        <i class="fas fa-plus me-2"></i>
                        Nhà cung cấp
                    </button>
                    
                    <!-- Import file -->
                    <button class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="fas fa-download me-2"></i>
                        Import file
                    </button>
                    <!-- Xuất file -->
                    <button class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="fas fa-upload me-2"></i>
                        Xuất file
                    </button>
                    <!-- Utility Icons -->
                    <div class="utility-icons d-flex align-items-center gap-1 ms-2">
                        <button class="btn btn-outline-light btn-sm" title="Chế độ xem">
                            <i class="fas fa-list text-muted"></i>
                        </button>
                        <button class="btn btn-outline-light btn-sm" title="Cài đặt">
                            <i class="fas fa-cog text-muted"></i>
                        </button>
                        <button class="btn btn-outline-light btn-sm" title="Trợ giúp">
                            <i class="fas fa-question-circle text-muted"></i>
                        </button>
                    </div>
                </div>
            </div>  
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Content Area -->
    <div class="content-area mt-4">
        <div class="row">
            <!-- Left Sidebar - Filter Section -->
            <div class="col-xl-3 col-lg-3 col-md-4 mb-4">
                <div class="p-2" style="background: #ffffff; border-radius: 8px; border: 1px solid #dee2e6;">
                    <div class="sidebar sidebar-filter">
                        <div class="filter-section">
                            <label>
                                Nhóm nhà cung cấp
                                <a href="#" class="create-link" style="margin-left: 52px;" data-bs-toggle="modal" data-bs-target="#createSupplierCategoryModal">Tạo mới</a>
                            </label>
                            <div>
                                <select class="form-select form-select-sm" name="supplier_group_id" onchange="filterSuppliers()">
                                    <option value="">Chọn nhóm</option>
                                    @foreach($supplierGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="filter-section">
                            <label>Trạng thái</label>
                            <select class="form-select form-select-sm" name="status" onchange="filterSuppliers()">
                                <option value="">Tất cả</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Tạm ngưng</option>
                                <option value="pending">Chờ duyệt</option>
                            </select>
                        </div>
                        <div class="filter-section">
                            <label>Tỉnh/Thành phố</label>
                            <select class="form-select form-select-sm" name="province" onchange="filterSuppliers()">
                                <option value="">Chọn tỉnh/thành</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Main Content -->
            <div class="col-xl-9 col-lg-9 col-md-8">
                <div class="main-content">
                    <!-- Table Section -->
                    <div class="p-2" style="background: #ffffff; border-radius: 8px; border: 1px solid #dee2e6;">
                        <div class="table-section">
                            <table class="table supplier-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input">
                                        </th>
                                        <th style="min-width: 100px;">Mã NCC</th>
                                        <th style="min-width: 200px;">Tên nhà cung cấp</th>
                                        <th>Số điện thoại</th>
                                        <th>Địa chỉ</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($suppliers as $supplier)
                                    <tr class="supplier-row" 
                                        data-supplier-id="{{ $supplier->id }}" 
                                        data-group-id="{{ $supplier->nhom_nha_cung_cap_id }}" 
                                        data-status="{{ $supplier->trang_thai }}" 
                                        data-province="{{ $supplier->khu_vuc }}"
                                        onclick="toggleSupplierDetail({{ $supplier->id }}, this)"
                                        style="cursor: pointer;">
                                        <td>
                                            <input type="checkbox" class="form-check-input">
                                        </td>
                                        <td><span class="supplier-code">{{ $supplier->ma_nha_cung_cap }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="supplier-name">{{ $supplier->ten_nha_cung_cap }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <div><i class=""></i> {{ $supplier->dien_thoai }}</div>
                                                @if($supplier->email)
                                                    <div><i class=""></i> {{ $supplier->email }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;">
                                                {{ $supplier->dia_chi }}
                                                @if($supplier->khu_vuc)
                                                    <br><small class="text-muted">{{ $supplier->khu_vuc }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($supplier->trang_thai == 'active')
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            @else
                                                <span class="badge bg-secondary">Không hoạt động</span>
                                            @endif
                                        </td>
                                        <td>{{ $supplier->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    
                                    <!-- Chi tiết supplier (ẩn mặc định) -->
                                    <tr id="detail-row-{{ $supplier->id }}" class="detail-row" style="display: none;">
                                        <td colspan="7" class="p-0">
                                            <div class="supplier-detail-container bg-light border-top">
                                                <div class="row p-4">
                                                    <!-- Thông tin chung -->
                                                    <div class="col-md-6">
                                                        <h6 class="text-primary mb-3">
                                                            <i></i>Thông tin chung
                                                        </h6>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td class="fw-bold" style="width: 140px;">Mã NCC:</td>
                                                                <td>{{ $supplier->ma_nha_cung_cap }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Tên NCC:</td>
                                                                <td>{{ $supplier->ten_nha_cung_cap }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Điện thoại:</td>
                                                                <td>{{ $supplier->dien_thoai }}</td>
                                                            </tr>
                                                            @if($supplier->email)
                                                            <tr>
                                                                <td class="fw-bold">Email:</td>
                                                                <td>{{ $supplier->email }}</td>
                                                            </tr>
                                                            @endif
                                                            <tr>
                                                                <td class="fw-bold">Nhóm NCC:</td>
                                                                <td>
                                                                    @if($supplier->category)
                                                                        <span class="badge bg-info">{{ $supplier->category->name }}</span>
                                                                    @else
                                                                        <span class="text-muted">Chưa phân nhóm</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Trạng thái:</td>
                                                                <td>
                                                                    @if($supplier->trang_thai == 'active')
                                                                        <span class="badge bg-success">Đang hoạt động</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Không hoạt động</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    
                                                    <!-- Thông tin liên hệ -->
                                                    <div class="col-md-6">
                                                        <h6 class="text-primary mb-3">
                                                            <i></i>Thông tin liên hệ
                                                        </h6>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td class="fw-bold" style="width: 140px;">Địa chỉ:</td>
                                                                <td>{{ $supplier->dia_chi }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Tỉnh/Thành:</td>
                                                                <td>{{ $supplier->khu_vuc }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Phường/Xã:</td>
                                                                <td>{{ $supplier->phuong_xa }}</td>
                                                            </tr>
                                                            @if($supplier->ten_cong_ty)
                                                            <tr>
                                                                <td class="fw-bold">Tên công ty:</td>
                                                                <td>{{ $supplier->ten_cong_ty }}</td>
                                                            </tr>
                                                            @endif
                                                            @if($supplier->ma_so_thue)
                                                            <tr>
                                                                <td class="fw-bold">Mã số thuế:</td>
                                                                <td>{{ $supplier->ma_so_thue }}</td>
                                                            </tr>
                                                            @endif
                                                            @if($supplier->ghi_chu)
                                                            <tr>
                                                                <td class="fw-bold">Ghi chú:</td>
                                                                <td>{{ $supplier->ghi_chu }}</td>
                                                            </tr>
                                                            @endif
                                                        </table>
                                                        
                                                        <!-- Action buttons -->
                                                        <div class="mt-3">
                                                            <button type="button" class="btn btn-success btn-sm me-2">
                                                                <i class="fas fa-edit me-1"></i>Chỉnh sửa
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-sm me-2">
                                                                <i class="fas fa-print me-1"></i>In thông tin
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash me-1"></i>Xóa
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i></i>
                                            <br>Không có nhà cung cấp nào
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Summary & Pagination -->
                    <div class="summary-section">
                        <div class="row">
                            <div class="col-md-6">
                                 <small class="text-muted">
                                     @if($suppliers->total() > 0)
                                         Tổng cộng: <strong>{{ $suppliers->total() }}</strong> nhà cung cấp | 
                                         Hiển thị: <strong>{{ $suppliers->firstItem() }}</strong> - <strong>{{ $suppliers->lastItem() }}</strong>
                                     @else
                                         Không có nhà cung cấp nào
                                     @endif
                                 </small>
                             </div>
                             <div class="col-md-6 d-flex justify-content-end">
                                 @if($suppliers->hasPages())
                                     {{ $suppliers->links() }}
                                 @endif
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<!-- Modal Tạo Nhóm Nhà Cung Cấp -->
<div class="modal fade" id="createSupplierCategoryModal" tabindex="-1" aria-labelledby="createSupplierCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSupplierCategoryModalLabel">
                    <i></i>Tạo nhóm nhà cung cấp mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createSupplierCategoryForm" action="{{ route('admin.supplier-categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên nhóm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="category_name" required 
                               placeholder="Ví dụ: Nhà cung cấp thuốc, Nhà cung cấp thiết bị y tế...">
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" id="category_description" rows="3" 
                                  placeholder="Mô tả chi tiết về nhóm nhà cung cấp này..."></textarea>
                        <div class="invalid-feedback" id="description-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status" id="category_status">
                            <option value="active">Kích hoạt</option>
                            <option value="inactive">Tạm ngưng</option>
                        </select>
                        <div class="invalid-feedback" id="status-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="createCategoryBtn">
                        <i class="fas fa-save me-2"></i>Tạo nhóm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include modal create supplier -->
@include('admin.products.Nhaphang.Suppliers.create')


@push('styles')
<style>
/* Header Control Bar */
.header-control-bar {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 1rem;
    margin-bottom: 1rem;
}

.header-title {
    color: #333;
    font-weight: 600;
}

/* Search Container */
.search-container {
    position: relative;
}

.search-container .input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

/* Action Buttons */
.action-buttons .btn {
    border-radius: 6px;
    font-size: 0.875rem;
}

/* Sidebar Filter */
.sidebar-filter {
    padding: 0;
}

.filter-section {
    margin-bottom: 1.5rem;
}

.filter-section label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}

.filter-section .form-select {
    border-radius: 6px;
    border-color: #dee2e6;
}

.create-link {
    color: #007bff;
    text-decoration: none;
    font-size: 0.875rem;
}

.create-link:hover {
    text-decoration: underline;
}

/* Table Styles */
.supplier-table {
    margin-bottom: 0;
}

.supplier-table thead th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
    padding: 0.75rem;
}

.supplier-table tbody td {
    border-color: #dee2e6;
    padding: 0.75rem;
    vertical-align: middle;
}

.supplier-table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Supplier Logo */
.supplier-logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

.supplier-logo {
    border-radius: 6px;
}

/* Contact Info */
.contact-info {
    font-size: 0.875rem;
    color: #6c757d;
}

.contact-info div {
    margin-bottom: 0.25rem;
}

/* Rating */
.rating {
    display: flex;
    align-items: center;
}

.rating i {
    font-size: 0.875rem;
}

/* Badges */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
}

/* Summary Section */
.summary-section {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 1rem;
    margin-top: 1rem;
}

/* Pagination */
.pagination .page-link {
    border-color: #dee2e6;
    color: #007bff;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #ffffff;
}

/* Responsive */
@media (max-width: 768px) {
    .header-control-bar .controls-section {
        flex-direction: column;
        gap: 1rem;
    }
    
    .search-container {
        width: 100%;
    }
    
    .action-buttons {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Filter suppliers function
function filterSuppliers() {
    const groupId = document.querySelector('select[name="supplier_group_id"]').value;
    const status = document.querySelector('select[name="status"]').value;
    const province = document.querySelector('select[name="province"]').value;
    const businessType = document.querySelector('select[name="business_type"]').value;
    const rating = document.querySelector('select[name="rating"]').value;
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    
    const supplierRows = document.querySelectorAll('.supplier-row');
    let visibleCount = 0;
    
    supplierRows.forEach(row => {
        let showRow = true;
        
        // Filter by group
        if (groupId && row.getAttribute('data-group-id') !== groupId) {
            showRow = false;
        }
        
        // Filter by status
        if (status && row.getAttribute('data-status') !== status) {
            showRow = false;
        }
        
        // Filter by province
        if (province && row.getAttribute('data-province') !== province) {
            showRow = false;
        }
        
        // Filter by business type
        if (businessType && row.getAttribute('data-business-type') !== businessType) {
            showRow = false;
        }
        
        // Filter by rating
        if (rating && parseInt(row.getAttribute('data-rating')) < parseInt(rating)) {
            showRow = false;
        }
        
        // Filter by search term
        if (searchTerm && showRow) {
            const supplierName = row.querySelector('.supplier-name')?.textContent.toLowerCase() || '';
            const supplierCode = row.querySelector('.supplier-code')?.textContent.toLowerCase() || '';
            
            const isMatch = supplierName.includes(searchTerm) || supplierCode.includes(searchTerm);
            
            if (!isMatch) {
                showRow = false;
            }
        }
        
        // Show/hide row
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update summary
    updateSupplierCount(visibleCount);
}

// Search suppliers function
function searchSuppliers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const supplierRows = document.querySelectorAll('.supplier-row');
    let visibleCount = 0;
    
    supplierRows.forEach(row => {
        const supplierName = row.querySelector('.supplier-name')?.textContent.toLowerCase() || '';
        const supplierCode = row.querySelector('.supplier-code')?.textContent.toLowerCase() || '';
        
        const isMatch = supplierName.includes(searchTerm) || supplierCode.includes(searchTerm);
        
        if (isMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    updateSupplierCount(visibleCount);
}

// Update supplier count
function updateSupplierCount(visibleCount) {
    const totalSuppliers = document.querySelectorAll('.supplier-row').length;
    const summaryElement = document.querySelector('.summary-section small');
    if (summaryElement) {
        summaryElement.innerHTML = `Tổng cộng: <strong>${totalSuppliers}</strong> nhà cung cấp | Hiển thị: <strong>${visibleCount}</strong>`;
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateSupplierCount(document.querySelectorAll('.supplier-row').length);
});

// Handle supplier category form submission
// Simple loading state for form submission
document.getElementById('createSupplierCategoryForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('createCategoryBtn');
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo...';
});

// Clear form validation errors
function clearFormErrors() {
    const errorElements = document.querySelectorAll('.invalid-feedback');
    const inputElements = document.querySelectorAll('.is-invalid');
    
    errorElements.forEach(el => el.textContent = '');
    inputElements.forEach(el => el.classList.remove('is-invalid'));
}

// Reset form when modal is hidden
document.getElementById('createSupplierCategoryModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('createSupplierCategoryForm');
    form.reset();
    clearFormErrors();
});
</script>

<!-- Supplier Detail Styles -->
<style>
    .supplier-row:hover {
        background-color: #f8f9fa !important;
    }
    
    .supplier-row.selected-row {
        background-color: #e3f2fd !important;
    }
    
    .supplier-detail-container {
        background-color: #f8f9fa;
    }
    
    .detail-row td {
        border-top: none !important;
    }
    
    /* Table styling giống danh sách hàng hóa */
    .supplier-detail-container .table {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 0;
    }
    
    .supplier-detail-container .table td {
        border: 1px solid #e9ecef;
        padding: 8px 12px;
        vertical-align: middle;
    }
    
    .supplier-detail-container .table td:first-child {
        background-color: #f1f3f4;
        font-weight: 600;
        color: #495057;
        width: 140px;
    }
    
    .supplier-detail-container .table td:last-child {
        background-color: white;
    }
    
    /* Header styling */
    .supplier-detail-container h6 {
        background-color: #e3f2fd;
        padding: 10px 15px;
        margin: 0 0 15px 0;
        border-radius: 6px;
    }
    
    /* Action buttons styling */
    .supplier-detail-container .btn {
        border-radius: 6px;
        font-size: 13px;
        padding: 6px 12px;
    }
</style>

<!-- Load supplier management JS -->
<script src="{{ asset('js/products/supplier/supplier-management.js') }}"></script>
@endpush

@endsection
