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
                                <a href="#" class="create-link" style="margin-left: 65px;" data-bs-toggle="modal" data-bs-target="#createSupplierCategoryModal">Tạo mới</a>
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
                                                    <div class="title-detail">
                                                        <button class="tab active" onclick="switchProductTab(4, 'info', this)">Thông tin</button>
                                                        <button class="tab active" onclick="switchProductTab(4, 'info', this)">Lịch sử nhập hàng</button>
                                                    </div>
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
                                                            <button type="button" class="btn btn-success btn-sm me-2" onclick="editSupplier({{ $supplier->id }})">
                                                                <i class="fas fa-edit"></i>Chỉnh sửa
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-sm me-2">
                                                                <i></i>In thông tin
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm">
                                                                <i></i>Xóa
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

{{-- include modal --}}
@include('admin.products.Nhaphang.Suppliers.partials.supplier-create-modal')
@include('admin.products.Nhaphang.Suppliers.partials.supplier-edit-modal')
    

@push('styles')
<link href="{{ asset('css/management/supplier-management.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/products/supplier/supplier-management.js') }}"></script>
@endpush

@endsection