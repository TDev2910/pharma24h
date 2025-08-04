@extends('layouts.admin')

@section('title', 'Quản lý hàng hóa')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Title Section -->
            <div class="title-section">
                <h4 class="header-title mb-0">Hàng hóa</h4>
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
                            <input type="text" class="form-control" placeholder="Theo mã, tên hàng">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="action-buttons d-flex align-items-center gap-2">
                    <!-- Dropdown Tạo mới -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center"
                            type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-plus me-2"></i>
                            Tạo mới
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createMedicineModal">
                                    Thuốc
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="#">Hàng hóa</a></li>
                            <li><a class="dropdown-item" href="#">Dịch vụ</a></li>
                            <li><a class="dropdown-item" href="#">Combo - đóng gói</a></li>
                        </ul>
                    </div>
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
    <!-- Content Area -->
    <div class="content-area mt-4">
        <div class="row">
            <!-- Left Sidebar - Filter Section -->
            <div class="col-xl-3 col-lg-3 col-md-4 mb-4">
                <div class="p-2" style="background: #ffffff; border-radius: 8px; border: 1px solid #dee2e6;">
                    <div class="sidebar sidebar-filter">
                        <div class="filter-section">
                            <label>
                                Nhóm hàng
                                <a href="#" class="create-link" data-bs-toggle="modal" data-bs-target="#createCategoryModal" style="margin-left: 115px;">Tạo mới</a>
                            </label>
                            <div>
                                <select class="form-select form-select-sm" name="category_id">
                                    <option value="">Chọn nhóm hàng</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="filter-section">
                            <label>Tồn kho</label>
                            <select class="form-select form-select-sm">
                                <option>Tất cả</option>
                                <option>Còn hàng</option>
                                <option>Hết hàng</option>
                                <option>Sắp hết</option>
                            </select>
                        </div>
                        {{-- 
                        <div class="filter-section">
                            <label>Dự kiến hết hàng</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="out_of_stock_all" name="out_of_stock" value="all" checked>
                                    <label for="out_of_stock_all">Toàn thời gian</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="out_of_stock_custom" name="out_of_stock" value="custom">
                                    <label for="out_of_stock_custom">Tùy chỉnh</label>
                                </div>
                            </div>
                        </div>
                        <div class="filter-section">
                            <label>Thời gian tạo</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="created_all" name="created_time" value="all" checked>
                                    <label for="created_all">Toàn thời gian</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="created_custom" name="created_time" value="custom">
                                    <label for="created_custom">Tùy chỉnh</label>
                                </div>
                            </div>
                        </div>
                        --}}
                        <div class="filter-section">
                            <label>Nhà cung cấp</label>
                            <select class="form-select form-select-sm">
                                <option>Chọn nhà cung cấp</option>
                                <option>Công ty A</option>
                                <option>Công ty B</option>
                            </select>
                        </div>
                        <div class="filter-section">
                            <label>Vị trí</label>
                            <select class="form-select form-select-sm">
                                <option>Chọn vị trí</option>
                                <option>Kho A</option>
                                <option>Kho B</option>
                            </select>
                        </div>
                        <div class="filter-section">
                            <label>Loại hàng</label>
                            <select class="form-select form-select-sm">
                                <option>Chọn loại hàng</option>
                                <option>Thuốc</option>
                                <option>Thiết bị y tế</option>
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
                            <table class="table product-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input">
                                        </th>
                                        <th>Ảnh</th>
                                        <th>Mã hàng</th>
                                        <th style="min-width: 200px;">Tên hàng</th>
                                        <th>Tên viết tắt</th>
                                        <th>Giá bán</th>
                                        <th>Giá vốn</th>
                                        <th>Tồn kho</th>
                                        <th>Thời gian tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($medicines as $medicine)
                                        <tr class="product-row" data-product-id="{{ $medicine->id }}" style="cursor: pointer;" onclick="toggleProductDetail({{ $medicine->id }}, this)">
                                            <td>
                                                <input type="checkbox" class="form-check-input" onclick="event.stopPropagation()">
                                            </td>
                                            <td>
                                                <div class="product-image-container">
                                                    <img src="{{ $medicine->image_url }}"
                                                        alt="{{ $medicine->ten_thuoc }}"
                                                        class="img-thumbnail product-image"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td><span class="product-code">{{ $medicine->ma_hang ?? 'N/A' }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="product-name">{{ $medicine->ten_thuoc ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td><span class="product-abbreviation">{{ $medicine->ten_viet_tat ?? 'N/A' }}</span></td>
                                            <td>{{ number_format($medicine->gia_ban ?? 0) }} VNĐ</td>
                                            <td>{{ number_format($medicine->gia_von ?? 0) }} VNĐ</td>
                                            <td>{{ $medicine->ton_thap_nhat ?? 0 }}</td>
                                            <td>{{ $medicine->created_at ? $medicine->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <!-- Expandable Detail Row -->
                                        <tr class="detail-row" id="detail-row-{{ $medicine->id }}" style="display: none;">
                                            <td colspan="10" class="p-0">
                                                <div class="detail-content">
                                                    <div class="row">
                                                        <!-- Ảnh sản phẩm -->
                                                        <div class="col-md-3">
                                                            <div class="product-image-detail-large">
                                                                <img id="productDetailImageLarge-{{ $medicine->id }}" src="{{ $medicine->image_url }}"
                                                                    alt="Product Image"
                                                                    class="img-fluid rounded"
                                                                    style="width: 100%; height: 200px; object-fit: cover;">
                                                            </div>
                                                        </div>
                                                        <!-- Thông tin sản phẩm -->
                                                        <div class="col-md-9">
                                                            <div class="product-info-detail">
                                                                <!-- Product Header -->
                                                                <div class="product-header mb-4">
                                                                    <h4 class="product-title mb-2">{{ $medicine->ten_thuoc ?? 'N/A' }}</h4>
                                                                    <div class="product-category mb-2">
                                                                        <small class="text-muted">Nhóm hàng: {{ $medicine->category->name ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="product-tags">
                                                                        <span class="badge bg-primary me-2">Thuốc</span>
                                                                        <span class="badge bg-light text-dark me-2">Bán trực tiếp</span>
                                                                        <span class="badge bg-warning">Không tích điểm</span>
                                                                    </div>
                                                                </div>
                                                                <!-- Tabs -->
                                                                <ul class="nav nav-tabs" id="productDetailTabs-{{ $medicine->id }}" role="tablist">
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link active" id="info-tab-{{ $medicine->id }}" data-bs-toggle="tab" data-bs-target="#info-{{ $medicine->id }}" type="button" role="tab" style="margin-left:-15px;">Thông tin</button>
                                                                    </li>
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link" id="description-tab-{{ $medicine->id }}" data-bs-toggle="tab" data-bs-target="#description-{{ $medicine->id }}" type="button" role="tab">Mô tả, ghi chú</button>
                                                                    </li>
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link" id="inventory-tab-{{ $medicine->id }}" data-bs-toggle="tab" data-bs-target="#inventory-{{ $medicine->id }}" type="button" role="tab">Tồn kho</button>
                                                                    </li>
                                                                </ul>
                                                                <!-- Tab content -->
                                                                <div class="tab-content mt-3" id="productDetailTabContent-{{ $medicine->id }}">
                                                                    <!-- Tab Thông tin -->
                                                                    <div class="tab-pane fade show active" id="info-{{ $medicine->id }}" role="tabpanel">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="info-group">
                                                                                    <h6 class="text-muted mb-3">Thông tin chung</h6>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered table-striped align-middle text-center">
                                                                                            <thead class="table-primary">
                                                                                                <tr>
                                                                                                    <th scope="col">Chỉ tiêu</th>
                                                                                                    <th scope="col">Giá trị</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td><strong>Mã hàng</strong></td>
                                                                                                    <td id="productDetailCodeLarge-{{ $medicine->id }}">{{ $medicine->ma_hang ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Mã vạch</strong></td>
                                                                                                    <td id="productDetailBarcode-{{ $medicine->id }}">{{ $medicine->ma_vach ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Giá vốn</strong></td>
                                                                                                    <td id="productDetailCostLarge-{{ $medicine->id }}">{{ number_format($medicine->gia_von ?? 0) }} VNĐ</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Giá bán</strong></td>
                                                                                                    <td class="text-success" id="productDetailPriceLarge-{{ $medicine->id }}">{{ number_format($medicine->gia_ban ?? 0) }} VNĐ</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Tên viết tắt</strong></td>
                                                                                                    <td id="productDetailAbbreviation-{{ $medicine->id }}">{{ $medicine->ten_viet_tat ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Vị trí</strong></td>
                                                                                                    <td id="productDetailPositionLarge-{{ $medicine->id }}">{{ $medicine->position->name ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Định mức tồn</strong></td>
                                                                                                    <td id="productDetailStockRange-{{ $medicine->id }}">{{ $medicine->ton_thap_nhat ?? 0 }} - {{ $medicine->ton_cao_nhat ?? '999,999,999' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Trọng lượng</strong></td>
                                                                                                    <td id="productDetailWeight-{{ $medicine->id }}">{{ $medicine->trong_luong ? $medicine->trong_luong . ' g' : 'N/A' }}</td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="info-group">
                                                                                    <h6 class="text-muted mb-3">Thông tin thuốc</h6>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered table-striped align-middle text-center">
                                                                                            <thead class="table-primary">
                                                                                                <tr>
                                                                                                    <th scope="col">Chỉ tiêu</th>
                                                                                                    <th scope="col">Giá trị</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td><strong>Mã thuốc</strong></td>
                                                                                                    <td id="productDetailDrugCode-{{ $medicine->id }}">{{ $medicine->ma_thuoc ?? 'Chưa có' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Hoạt chất</strong></td>
                                                                                                    <td id="productDetailActiveIngredient-{{ $medicine->id }}">{{ $medicine->hoat_chat ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Đường dùng</strong></td>
                                                                                                    <td id="productDetailDrugRouteLarge-{{ $medicine->id }}">{{ $medicine->drugRoute->name ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Số đăng ký</strong></td>
                                                                                                    <td id="productDetailRegistration-{{ $medicine->id }}">{{ $medicine->so_dang_ky ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Hàm lượng</strong></td>
                                                                                                    <td id="productDetailDosage-{{ $medicine->id }}">{{ $medicine->ham_luong ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Quy cách đóng gói</strong></td>
                                                                                                    <td id="productDetailPackaging-{{ $medicine->id }}">{{ $medicine->quy_cach_dong_goi ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Hãng sản xuất</strong></td>
                                                                                                    <td id="productDetailManufacturerLarge-{{ $medicine->id }}">
                                                                                                        @if($medicine->manufacturer)
                                                                                                            {{ $medicine->manufacturer->name }}
                                                                                                        @else
                                                                                                            N/A (manufacturer_id: {{ $medicine->manufacturer_id }})
                                                                                                        @endif
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Nước sản xuất</strong></td>
                                                                                                    <td id="productDetailCountry-{{ $medicine->id }}">{{ $medicine->nuoc_san_xuat ?? 'Việt Nam' }}</td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Tab Mô tả -->
                                                                    <div class="tab-pane fade" id="description-{{ $medicine->id }}" role="tabpanel">
                                                                        <div class="description-content">
                                                                            <h6 class="text-muted mb-3">Mô tả sản phẩm</h6>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-bordered table-striped align-middle text-center">
                                                                                    <thead class="table-primary">
                                                                                        <tr>
                                                                                            <th scope="col">Nội dung</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td id="productDetailDescriptionLarge-{{ $medicine->id }}" class="text-start">
                                                                                                {{ $medicine->mo_ta ?? 'Chưa có mô tả' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Tab Tồn kho trong detail -->
                                                                    <div class="tab-pane fade" id="inventory-{{ $medicine->id }}" role="tabpanel">
                                                                        <div class="inventory-content">
                                                                            <h6 class="text-muted mb-3">Thông tin tồn kho</h6>
                                                                            <div class="table-responsive" style="max-width: 500px;">
                                                                                <table class="table table-bordered table-striped align-middle text-center">
                                                                                    <thead class="table-primary">
                                                                                        <tr>
                                                                                            <th scope="col">Chỉ tiêu</th>
                                                                                            <th scope="col">Giá trị</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td><strong>Tồn kho hiện tại</strong></td>
                                                                                            <td id="productDetailCurrentStock-{{ $medicine->id }}">{{ $medicine->ton_thap_nhat ?? 0 }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><strong>Tồn kho tối đa</strong></td>
                                                                                            <td id="productDetailMaxStock-{{ $medicine->id }}">{{ $medicine->ton_cao_nhat ?? '999,999,999' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><strong>Tồn kho tối thiểu</strong></td>
                                                                                            <td id="productDetailMinStock-{{ $medicine->id }}">{{ $medicine->ton_thap_nhat ?? 0 }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><strong>Định mức tồn</strong></td>
                                                                                            <td id="productDetailStockRange-{{ $medicine->id }}">{{ $medicine->ton_thap_nhat ?? 0 }} - {{ $medicine->ton_cao_nhat ?? '999,999,999' }}</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Action Buttons -->
                                                                <div class="action-buttons-container">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div class="left-actions">
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="showDeleteConfirmation({{ $medicine->id }}, '{{ $medicine->ma_hang }}', '{{ $medicine->ten_thuoc }}')">
                                                                                <i class="fas fa-trash"></i> Xóa
                                                                            </button>
                                                                        </div>
                                                                        <div class="right-actions">
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #1db46a; border-color: #1db46a; color: white;" onclick="openEditMedicineModal({{ $medicine->id }})">
                                                                                <i class="fas fa-edit"></i> Chỉnh sửa
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="printLabel({{ $medicine->id }})">
                                                                                <i class="fas fa-print"></i> In tem mã
                                                                            </button>
                                                                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                                                                <i class="fas fa-ellipsis-h"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <br>Chưa có sản phẩm nào
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
                                    Tổng cộng: <strong>{{ $medicines->total() ?? 0 }}</strong> sản phẩm |
                                    Hiển thị: <strong>{{ $medicines->firstItem() ?? 0 }}</strong> - <strong>{{ $medicines->lastItem() ?? 0 }}</strong>
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                @if(isset($medicines) && $medicines->hasPages())
                                    <nav aria-label="Page navigation">
                                        {{ $medicines->links() }}
                                    </nav>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo nhóm hàng -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Tạo nhóm hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="category-name" class="form-label">Tên nhóm hàng</label>
                    <input type="text" class="form-control" id="category-name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="parent-category" class="form-label">Nhóm cha (nếu có)</label>
                    <select class="form-select" id="parent-category" name="parent_id">
                        <option value="">Không có nhóm cha</option>
                        @foreach($parentCategories ?? [] as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
                <button type="submit" class="btn btn-success">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Include Modal Components -->
@include('admin.products.Danhsachhanghoa.createmedicine')
@include('admin.products.Danhsachhanghoa.editmedicine')

@push('styles')
<style>
    /* Header Control Bar Styling */
    .header-control-bar {
        background: white;
        border-radius: 0;
        border-bottom: 1px solid #e9ecef;
        padding: 16px 0;
        margin-bottom: 0;
    }
    
    .header-title {
        color: #212529;
        font-weight: 600;
        font-size: 1.375rem;
    }
    
    /* Content Area Styling */
    .content-area {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .sidebar {
        background: white;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        height: fit-content;
        margin-bottom: 0;
        border: 1px solid #e9ecef;
    }
    
    .sidebar h5 {
        margin-top: -8px;
        margin-bottom: 15px;
        font-weight: 600;
        color: #495057;
    }
    
    .main-content {
        background: white;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 0;
        min-height: 600px;
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    
    .filter-section { 
        margin-bottom: 20px; 
    }
    
    .filter-section label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }
    
    .create-link {
        color: #20c997;
        text-decoration: none;
        font-weight: 500;
        float: right;
        font-size: 12px;
    }
    
    .create-link:hover { 
        color: #17a085; 
    }
    
    .radio-group { 
        display: flex; 
        flex-direction: column; 
        gap: 8px; 
    }
    
    .radio-item { 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        font-size: 13px;
    }
    
         /* Custom Filter Sizes */
     .filter-section .form-select-sm {
         height: 32px;
         font-size: 13px;
         padding: 4px 8px;
         width: 100%;
         border-radius: 4px;
         border: 1px solid #dee2e6;
     }
     
     .filter-section select[name="category_id"] {
         height: 28px;
         font-size: 12px;
         width: 100%;
     }
     
     .filter-section select:not([name="category_id"]) {
         height: 30px;
         font-size: 13px;
         width: 100%;
     }
    
    /* Table Improvements */
    .table-section { 
        overflow-x: auto; 
        margin-top: 0;
    }
    
    .product-table { 
        margin-bottom: 0; 
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    
    .product-table th {
        background-color: #e3f2fd;
        border-top: none;
        font-weight: 600;
        color: #1976d2;
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px 8px;
        font-size: 13px;
        border-bottom: 2px solid #bbdefb;
    }
    
    .product-table td {
        vertical-align: middle;
        border-color: #e9ecef;
        padding: 12px 8px;
        font-size: 13px;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .product-table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    .product-code { 
        color: #6c757d; 
        font-weight: 500; 
        font-size: 12px;
    }
    
    .product-name { 
        color: #495057; 
        font-weight: 500; 
        font-size: 13px;
    }
    
    .product-abbreviation { 
        color: #6c757d; 
        font-size: 0.875rem; 
        font-style: italic; 
    }
    
    /* Summary Section */
    .summary-section {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 15px 20px;
        margin: 0;
        border-radius: 0;
    }
    
    /* Search Container */
    .search-container .input-group {
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #dee2e6;
    }
    
    .search-container .input-group-text {
        background: white;
        border: none;
        padding: 8px 12px;
    }
    
    .search-container .form-control {
        border: none;
        padding: 8px 12px;
        font-size: 14px;
    }
    
    .search-container .form-control:focus {
        box-shadow: none;
        border-color: transparent;
    }
    
    .search-container .btn {
        background: white;
        border: none;
        padding: 8px 12px;
    }
    
    .search-container .btn:hover {
        background: #f8f9fa;
    }
    
    /* Action Buttons */
    .action-buttons .btn {
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        padding: 8px 16px;
        white-space: nowrap;
    }
    
    
    .btn-outline-secondary {
        border-color: #ced4da;
        color: #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        border-color: #ced4da;
        color: #495057;
    }
    
    /* Utility Icons */
    .utility-icons .btn {
        border: 1px solid #e9ecef;
        background: white;
        padding: 6px 8px;
        border-radius: 4px;
    }
    
    .utility-icons .btn:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
    }
    
    .utility-icons .btn i {
        font-size: 13px;
    }
    
         /* Spacing utilities */
     .mb-2 {
         margin-bottom: 0.5rem !important;
     }
     
     .mb-3 {
         margin-bottom: 1rem !important;
     }
     
     .mb-4 {
         margin-bottom: 1.5rem !important;
     }
    
    /* Container max-width for large screens */
    @media (min-width: 1400px) {
        .container-fluid {
            max-width: 1400px !important;
            margin: 0 auto;
        }
        
        .content-area {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-control-bar {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Typography scaling for large screens */
        .header-title {
            font-size: 1.5rem;
        }
        
        .product-table th,
        .product-table td {
            font-size: 14px;
        }
        
        .filter-section label {
            font-size: 15px;
        }
        
        .btn {
            font-size: 15px;
        }
        .container-fluid {
            max-width: 1400px !important;
            margin: 0 auto;
        }
        
        .content-area {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-control-bar {
            max-width: 1400px;
            margin: 0 auto;
        }
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .header-control-bar .d-flex {
            flex-direction: column;
            align-items: stretch !important;
            gap: 1rem;
        }
        
        .controls-section {
            flex-direction: column;
            align-items: stretch !important;
        }
        
        .action-buttons {
            flex-wrap: wrap;
            justify-content: center;
        }       
        .search-container {
            order: -1;
        }
    }
    
    @media (max-width: 768px) {
        .header-control-bar {
            padding: 12px 0;
        }
        
        .header-title {
            font-size: 1.25rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .action-buttons .btn {
            font-size: 13px;
            padding: 6px 12px;
        }
        
        .search-container .input-group {
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .search-container .form-control {
            border: 1px solid #dee2e6;
            border-right: none;
        }
        
        .search-container .input-group-text {
            background: white;
            border: 1px solid #dee2e6;
            border-right: none;
        }
        
        .search-container .btn {
            border: 1px solid #dee2e6;
            border-left: none;
        }
    }
    
    /* Product Image Container */
    .product-image-container {
        cursor: pointer;
        display: inline-block;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .product-image-container:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .product-image-container:hover .product-image {
        filter: brightness(1.1);
    }
    
    /* Detail Row Styling */
    .detail-row {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    
    .detail-content {
        padding: 20px;
    }
    
    .product-image-detail-large {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        background: #f8f9fa;
        text-align: center;
    }
    
    /* Product Header Styling */
    .product-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 15px;
    }
    
    .product-title {
        color: #495057;
        font-weight: 600;
        margin: 0;
    }
    
    .product-category {
        color: #6c757d;
    }
    
    .product-tags .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    
    /* Tabs Styling */
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #20c997;
        border-bottom: 2px solid #20c997;
        background: none;
    }
    
    .tab-content {
        padding: 20px 0;
    }
    
    /* Info Groups */
    .info-group {
        margin-bottom: 20px;
    }
    
    /* Action Buttons Container */
    .action-buttons-container {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 15px 20px;
        margin: 20px -20px -20px -20px;
    }
    
    .left-actions .btn {
        border-radius: 4px;
    }
    
    .right-actions .btn {
        border-radius: 4px;
    }
    
    /* Product Row Hover */
    .product-row {
        transition: background-color 0.2s ease;
    }
    
    .product-row:hover {
        background-color: #f8f9fa !important;
    }
    
    .selected-row {
        background-color: #e3f2fd !important;
    }
    
    /* Ensure detail row is visible when displayed */
    .detail-row[style*="display: table-row"] {
        display: table-row !important;
    }
    
    /* Delete animation styles */
    .product-row.deleting,
    .detail-row.deleting {
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateX(-100%);
    }
    
    /* Loading state for delete button */
    .btn-outline-danger:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Spinner animation */
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Success message styling */
    .success-message {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Debug modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded');
        // Check if modal element exists
        const modal = document.getElementById('createCategoryModal');
        if (modal) {
            console.log('Modal found:', modal);
        } else {
            console.log('Modal not found');
        }
        // Check if Bootstrap is loaded
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap loaded');
        } else {
            console.log('Bootstrap not loaded');
        }
        // Test modal trigger
        const createLink = document.querySelector('[data-bs-target="#createCategoryModal"]');
        if (createLink) {
            console.log('Create link found:', createLink);
            createLink.addEventListener('click', function(e) {
                console.log('Create link clicked');
            });
        } else {
            console.log('Create link not found');
        }
        // Debug form submission
        const categoryForm = document.querySelector('#createCategoryModal form');
        if (categoryForm) {
            console.log('Category form found:', categoryForm);
            categoryForm.addEventListener('submit', function(e) {
                console.log('Form submitted');
                const formData = new FormData(this);
                console.log('Form data:', Object.fromEntries(formData));
            });
        } else {
            console.log('Category form not found');
        }
        // Test manual modal opening
        window.testModal = function() {
            const modal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
            modal.show();
        };
    });

    // Toggle hiển thị thông tin chi tiết sản phẩm
    window.toggleProductDetail = function(productId, element) {
        const detailRow = document.getElementById(`detail-row-${productId}`);
        if (!detailRow) return;
        const isVisible = detailRow.style.display !== 'none';
        // Đóng tất cả các detail rows khác
        document.querySelectorAll('.detail-row').forEach(row => {
            row.style.display = 'none';
        });
        // Xóa highlight từ tất cả các hàng
        document.querySelectorAll('.product-table tbody tr').forEach(row => {
            row.classList.remove('selected-row');
        });
        if (!isVisible) {
            // Mở detail row
            detailRow.style.display = 'table-row';
            // Highlight hàng được chọn
            const selectedRow = element.closest('tr');
            if (selectedRow) {
                selectedRow.classList.add('selected-row');
            }
            // Scroll đến detail row
            detailRow.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    }

    // Format tiền tệ
    function formatCurrency(amount) {
        if (!amount) return '0 VNĐ';
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    // Placeholder functions for new actions
    window.deleteMedicine = function(medicineId) {
        if (confirm('Bạn có chắc chắn muốn xóa thuốc này?')) {
            // Tạo form để gửi request DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/medicines/${medicineId}`;
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

    // Function để mở modal chỉnh sửa sản phẩm
    window.openEditMedicineModal = function(medicineId) {
        console.log('Opening edit modal for medicine ID:', medicineId);
        // Gọi API để lấy thông tin sản phẩm
        fetch(`/admin/medicines/${medicineId}/detail`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    const medicine = data.product;
                    console.log('Medicine data:', medicine);
                    // Populate form fields
                    const fields = {
                        'edit_ma_hang': medicine.ma_hang || '',
                        'edit_ma_vach': medicine.ma_vach || '',
                        'edit_ten_thuoc': medicine.ten_thuoc || '',
                        'edit_ten_viet_tat': medicine.ten_viet_tat || '',
                        'edit_nhom_hang_id': medicine.nhom_hang_id || '',
                        'edit_gia_von': medicine.gia_von || 0,
                        'edit_gia_ban': medicine.gia_ban || 0,
                        'edit_so_dang_ky': medicine.so_dang_ky || '',
                        'edit_hoat_chat': medicine.hoat_chat || '',
                        'edit_ham_luong': medicine.ham_luong || '',
                        'edit_duong_dung_select': medicine.drugusage_id || '',
                        'edit_manufacturer_select': medicine.manufacturer_id || '',
                        'edit_nuoc_san_xuat': medicine.nuoc_san_xuat || '',
                        'edit_quy_cach_dong_goi': medicine.quy_cach_dong_goi || '',
                        'edit_ton_thap_nhat': medicine.ton_thap_nhat || 0,
                        'edit_ton_cao_nhat': medicine.ton_cao_nhat || 999999999,
                        'edit_position_select': medicine.position_id || '',
                        'edit_trong_luong': medicine.trong_luong || 0,
                        'edit_don_vi_tinh_input': medicine.don_vi_tinh || '',
                        'edit_mo_ta': medicine.mo_ta || ''
                    };
                    // Set form values
                    Object.keys(fields).forEach(fieldId => {
                        const element = document.getElementById(fieldId);
                        if (element) {
                            element.value = fields[fieldId];
                            console.log(`Set ${fieldId} to:`, fields[fieldId]);
                        } else {
                            console.log(`Element not found: ${fieldId}`);
                        }
                    });
                    // Set checkbox
                    const banTrucTiep = document.getElementById('edit_ban_truc_tiep');
                    if (banTrucTiep) {
                        banTrucTiep.checked = medicine.ban_truc_tiep == 1;
                    }
                    // Set form action
                    const form = document.getElementById('editMedicineForm');
                    if (form) {
                        form.action = `/admin/medicines/${medicineId}`;
                        console.log('Form action set to:', form.action);
                    } else {
                        console.log('Form not found: editMedicineForm');
                    }
                    // Show current image if exists
                    if (medicine.image) {
                        const preview = document.getElementById('edit-image-preview');
                        const placeholder = document.getElementById('edit-image-placeholder');
                        if (preview && placeholder) {
                            preview.src = `/storage/${medicine.image}`;
                            preview.style.display = 'block';
                            placeholder.style.display = 'none';
                        }
                    }
                    // Open modal
                    const modalElement = document.getElementById('editMedicineModal');
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                        console.log('Modal opened successfully');
                    } else {
                        console.log('Modal element not found: editMedicineModal');
                        alert('Không tìm thấy modal chỉnh sửa!');
                    }
                } else {
                    alert('Không thể tải thông tin thuốc!');
                }
            })
            .catch(error => {
                console.error('Error loading medicine data:', error);
                alert('Đã xảy ra lỗi khi tải thông tin thuốc!');
            });
    }
    // Delete confirmation modal functions
    window.showDeleteConfirmation = function(medicineId, medicineCode, medicineName) {
        // Set modal content
        document.getElementById('deleteMedicineId').value = medicineId;
        document.getElementById('deleteMedicineCode').textContent = medicineCode;
        document.getElementById('deleteMedicineName').textContent = medicineName;
        // Show modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        deleteModal.show();
    }

    window.confirmDelete = function() {
        const medicineId = document.getElementById('deleteMedicineId').value;
        const form = document.getElementById('deleteMedicineForm');
        form.action = `/admin/medicines/${medicineId}`;
        form.submit();
    }
</script>
@endpush

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold mb-3" id="deleteConfirmationModalLabel">
                    Xóa hàng hóa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-0">
                    Hệ thống sẽ xóa hoàn toàn hàng hóa
                    <span id="deleteMedicineCode" class="fw-bold text-dark" style="color: #000000 !important;"></span>
                    (<span id="deleteMedicineName" class="fw-bold"></span>)
                    trên toàn bộ chi nhánh nhưng vẫn giữ thông tin hàng hóa trong các giao dịch lịch sử nếu có.
                    Bạn có chắc chắn muốn xóa?
                </p>
            </div>
            <div class="modal-footer border-0 pt-1">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Bỏ qua
                </button>
                <button type="button" class="btn" style="background-color: #1db46a; border-color: #1db46a; color: white;" onclick="confirmDelete()">
                    Đồng ý
                </button>
            </div>
        </div>
    </div>
</div>
@endsection         