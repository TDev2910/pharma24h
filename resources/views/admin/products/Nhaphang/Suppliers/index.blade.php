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
                        <div class="filter-section">
                            <label>Loại hình</label>
                            <select class="form-select form-select-sm" name="business_type" onchange="filterSuppliers()">
                                <option value="">Chọn loại hình</option>
                                @foreach($businessTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-section">
                            <label>Đánh giá</label>
                            <select class="form-select form-select-sm" name="rating" onchange="filterSuppliers()">
                                <option value="">Tất cả</option>
                                <option value="5">5 sao</option>
                                <option value="4">4 sao trở lên</option>
                                <option value="3">3 sao trở lên</option>
                                <option value="2">2 sao trở lên</option>
                                <option value="1">1 sao trở lên</option>
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
                                        <th>Logo</th>
                                        <th style="min-width: 100px;">Mã NCC</th>
                                        <th style="min-width: 200px;">Tên nhà cung cấp</th>
                                        <th>Liên hệ</th>
                                        <th>Địa chỉ</th>
                                        <th>Trạng thái</th>
                                        <th>Đánh giá</th>
                                        <th>Thời gian tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($suppliers as $supplier)
                                    <tr class="supplier-row" 
                                        data-supplier-id="{{ $supplier->id }}" 
                                        data-group-id="{{ $supplier->group_id }}" 
                                        data-status="{{ $supplier->status }}" 
                                        data-province="{{ $supplier->province }}" 
                                        data-business-type="{{ $supplier->business_type }}" 
                                        data-rating="{{ $supplier->rating }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input">
                                        </td>
                                        <td>
                                            <div class="supplier-logo-container">
                                                <img src="{{ $supplier->logo }}"
                                                    alt="{{ $supplier->name }}"
                                                    class="img-thumbnail supplier-logo"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                        </td>
                                        <td><span class="supplier-code">{{ $supplier->code }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="supplier-name">{{ $supplier->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <div><i class="fas fa-phone me-1"></i> {{ $supplier->phone }}</div>
                                                <div><i class="fas fa-envelope me-1"></i> {{ $supplier->email }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $supplier->address }}</td>
                                        <td>
                                            @if($supplier->status == 'active')
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            @elseif($supplier->status == 'inactive')
                                                <span class="badge bg-secondary">Tạm ngưng</span>
                                            @else
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $supplier->rating)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                                <span class="ms-1">({{ $supplier->rating }}.0)</span>
                                            </div>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($supplier->created_at)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
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
                                     Tổng cộng: <strong>{{ $suppliers->count() }}</strong> nhà cung cấp | Hiển thị: <strong>1</strong> - <strong>{{ $suppliers->count() }}</strong>
                                 </small>
                             </div>
                            <div class="col-md-6 text-md-end">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">Trước</a>
                                        </li>
                                        <li class="page-item active">
                                            <a class="page-link" href="#">1</a>
                                        </li>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#">Sau</a>
                                        </li>
                                    </ul>
                                </nav>
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
@endpush

@endsection
