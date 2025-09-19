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
                            <input type="text" class="form-control" id="searchInput" placeholder="Theo mã, tên hàng" onkeyup="searchProducts()">
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
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createGoodsModal">
                                    Hàng hóa
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                                    Dịch vụ
                                </a>
                            </li>
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
                                Nhóm hàng
                                <a href="#" class="create-link" data-bs-toggle="modal" data-bs-target="#createCategoryModal" style="margin-left: 90px;">Tạo mới</a>
                            </label>
                            <div class="category-dropdown-container">
                                <div class="category-dropdown-header" onclick="toggleCategoryDropdown()">
                                    <span id="selectedCategoryText">Chọn nhóm hàng</span>
                                    <div class="category-header-actions">
                                        <button type="button" class="btn-reset-category" onclick="resetCategorySelection(event)" title="Xóa lựa chọn" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <i class="fas fa-chevron-down" id="categoryDropdownIcon"></i>
                                    </div>
                                </div>
                                <div id="categoriesListContainer" class="category-dropdown-content" style="display: none;">
                                    <!-- Categories sẽ hiển thị ở đây khi mở dropdown -->
                                    <div class="category-loading text-center py-3">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <small class="text-muted">Đang tải...</small>
                                    </div>
                                </div>
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
                                         
                        <div class="filter-section">
                            <label>Nhà cung cấp</label>
                            <select class="form-select form-select-sm" name="manufacturer_id" onchange="filterProducts()">
                                <option value="">Chọn nhà cung cấp</option>
                                @foreach($manufacturers as $manufacturer)
                                    <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-section">
                            <label>Vị trí</label>
                            <select class="form-select form-select-sm" name="position_id" onchange="filterProducts()">
                                <option value="">Chọn vị trí</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-section">
                            <label>Loại hàng</label>
                            <select class="form-select form-select-sm" name="product_type" onchange="filterProducts()">
                                <option value="">Chọn loại hàng</option>
                                <option value="medicine">Thuốc</option>
                                <option value="goods">Hàng hóa</option>
                                <option value="service">Dịch vụ</option>
                                <option value="combo">Combo - đóng gói</option>
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
                                        <th>Giá bán</th>
                                        <th>Giá vốn</th>
                                        <th>Tồn kho</th>
                                        <th>Thời gian tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Hiển thị thuốc -->
                                    @forelse($medicines as $medicine)
                                        <tr class="product-row medicine-row" 
                                            data-product-id="{{ $medicine->id }}" 
                                            data-category-id="{{ $medicine->nhom_hang_id }}"
                                            data-manufacturer-id="{{ $medicine->manufacturer_id }}"
                                            data-position-id="{{ $medicine->position_id }}"
                                            style="cursor: pointer;" 
                                            onclick="toggleProductDetail({{ $medicine->id }}, this)">
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
                                            <td>{{ $medicine->gia_ban_formatted }}</td>
                                            <td>{{ $medicine->gia_von_formatted }}</td>
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
                                                                                        <table class="table table-bordered">
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
                                                                                                    <td id="productDetailCostLarge-{{ $medicine->id }}">{{ $medicine->gia_von_formatted ?? '0 VND' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Giá bán</strong></td>
                                                                                                    <td class="text-success" id="productDetailPriceLarge-{{ $medicine->id }}">{{ $medicine->gia_ban_formatted ?? '0 VND' }}</td>
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
                                                                                        <table class="table table-bordered">
                                                                                            <tbody>
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
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="openUnitModal({{ $medicine->id }})">
                                                                                <i class="fas fa-cog"></i> Thiết lập đơn vị tính
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
                                                <br>Chưa có thuốc nào
                                            </td>
                                        </tr>
                                    @endforelse
                                    
                                    <!-- Hiển thị hàng hóa -->
                                    @forelse($goods as $good)
                                        <tr class="product-row goods-row" 
                                            data-product-id="goods-{{ $good->id }}" 
                                            data-category-id="{{ $good->nhom_hang_id }}"
                                            data-manufacturer-id="{{ $good->manufacturer_id }}"
                                            data-position-id="{{ $good->position_id }}"
                                            style="cursor: pointer;" 
                                            onclick="toggleGoodsDetail({{ $good->id }}, this)">
                                            <td>
                                                <input type="checkbox" class="form-check-input" onclick="event.stopPropagation()">
                                            </td>
                                            <td>
                                                <div class="product-image-container">
                                                    <img src="{{ $good->image_url }}"
                                                    alt="{{ $good->ten_hang_hoa }}"
                                                    class="img-thumbnail product-image"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td><span class="product-code">{{ $good->ma_hang ?? 'N/A' }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="product-name">{{ $good->ten_hang_hoa ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td><span class="product-abbreviation">{{ $good->ten_viet_tat ?: '' }}</span></td>
                                            <td>{{ $good->gia_ban_formatted ?? '0 VND' }}</td>
                                            <td>{{ $good->gia_von_formatted ?? '0 VND' }}</td>
                                            <td>{{ $good->ton_kho ?: $good->ton_thap_nhat ?: 0 }}</td>
                                            <td>{{ $good->created_at ? $good->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <!-- Expandable Detail Row cho hàng hóa -->
                                        <tr class="detail-row" id="detail-row-goods-{{ $good->id }}" style="display: none;">
                                            <td colspan="10" class="p-0">
                                                <div class="detail-content">
                                                    <div class="row">
                                                        <!-- Ảnh sản phẩm -->
                                                        <div class="col-md-3">
                                                            <div class="product-image-detail-large">
                                                                <img id="productDetailImageLarge-goods-{{ $good->id }}" src="{{ $good->image_url }}"
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
                                                                    <h4 class="product-title mb-2">{{ $good->ten_hang_hoa ?? 'N/A' }}</h4>
                                                                    <div class="product-category mb-2">
                                                                        <small class="text-muted">Nhóm hàng: {{ $good->category->name ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="product-tags">
                                                                        <span class="badge bg-success me-2">Hàng hóa</span>
                                                                        <span class="badge bg-light text-dark me-2">Bán trực tiếp</span>
                                                                        <span class="badge bg-warning">Không tích điểm</span>
                                                                    </div>
                                                                </div>
                                                                <!-- Tabs -->
                                                                <ul class="nav nav-tabs" id="productDetailTabs-goods-{{ $good->id }}" role="tablist">
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link active" id="info-tab-goods-{{ $good->id }}" data-bs-toggle="tab" data-bs-target="#info-goods-{{ $good->id }}" type="button" role="tab" style="margin-left:-15px;">Thông tin</button>
                                                                    </li>
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link" id="description-tab-goods-{{ $good->id }}" data-bs-toggle="tab" data-bs-target="#description-goods-{{ $good->id }}" type="button" role="tab">Mô tả, ghi chú</button>
                                                                    </li>
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link" id="inventory-tab-goods-{{ $good->id }}" data-bs-toggle="tab" data-bs-target="#inventory-goods-{{ $good->id }}" type="button" role="tab">Tồn kho</button>
                                                                    </li>
                                                                </ul>
                                                                <!-- Tab content -->
                                                                <div class="tab-content mt-3" id="productDetailTabContent-goods-{{ $good->id }}">
                                                                    <!-- Tab Thông tin -->
                                                                    <div class="tab-pane fade show active" id="info-goods-{{ $good->id }}" role="tabpanel">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="info-group">
                                                                                    <h6 class="text-muted mb-3">Thông tin chung</h6>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered table-striped align-middle text-center">                                                                                          
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td><strong>Mã hàng</strong></td>
                                                                                                    <td id="productDetailCodeLarge-goods-{{ $good->id }}">{{ $good->ma_hang ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Mã vạch</strong></td>
                                                                                                    <td id="productDetailBarcode-goods-{{ $good->id }}">{{ $good->ma_vach ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Giá vốn</strong></td>
                                                                                                    <td id="productDetailCostLarge-goods-{{ $good->id }}">{{ $good->gia_von_formatted ?? '0 VND' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Giá bán</strong></td>
                                                                                                    <td class="text-success" id="productDetailPriceLarge-goods-{{ $good->id }}">{{ $good->gia_ban_formatted ?? '0 VND' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Đơn vị tính</strong></td>
                                                                                                    <td id="productDetailUnit-goods-{{ $good->id }}">{{ $good->don_vi_tinh ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Vị trí</strong></td>
                                                                                                    <td id="productDetailPositionLarge-goods-{{ $good->id }}">{{ $good->position->name ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Định mức tồn</strong></td>
                                                                                                    <td id="productDetailStockRange-goods-{{ $good->id }}">{{ $good->ton_thap_nhat ?? 0 }} - {{ $good->ton_cao_nhat ?? '999,999,999' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Trọng lượng</strong></td>
                                                                                                    <td id="productDetailWeight-goods-{{ $good->id }}">{{ $good->trong_luong ? $good->trong_luong . ' g' : 'N/A' }}</td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="info-group">
                                                                                    <h6 class="text-muted mb-3">Thông tin hàng hóa</h6>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered table-striped align-middle text-center">
                                                                                           
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td><strong>Quy cách đóng gói</strong></td>
                                                                                                    <td id="productDetailPackaging-goods-{{ $good->id }}">{{ $good->quy_cach_dong_goi ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Hãng sản xuất</strong></td>
                                                                                                    <td id="productDetailManufacturerLarge-goods-{{ $good->id }}">
                                                                                                        @if($good->manufacturer)
                                                                                                            {{ $good->manufacturer->name }}
                                                                                                        @else
                                                                                                            N/A (manufacturer_id: {{ $good->manufacturer_id }})
                                                                                                        @endif
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Nước sản xuất</strong></td>
                                                                                                    <td id="productDetailCountry-goods-{{ $good->id }}">{{ $good->nuoc_san_xuat ?? 'Việt Nam' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Quản lý theo lô</strong></td>
                                                                                                    <td id="productDetailBatchManagement-goods-{{ $good->id }}">{{ $good->quan_ly_theo_lo ? 'Có' : 'Không' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Bán trực tiếp</strong></td>
                                                                                                    <td id="productDetailDirectSale-goods-{{ $good->id }}">{{ $good->ban_truc_tiep ? 'Có' : 'Không' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Khách đặt</strong></td>
                                                                                                    <td id="productDetailCustomerOrder-goods-{{ $good->id }}">{{ $good->khach_dat ? 'Có' : 'Không' }}</td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Tab Mô tả -->
                                                                    <div class="tab-pane fade" id="description-goods-{{ $good->id }}" role="tabpanel">
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
                                                                                            <td id="productDetailDescriptionLarge-goods-{{ $good->id }}" class="text-start">
                                                                                                {{ $good->mo_ta ?? 'Chưa có mô tả' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Tab Tồn kho trong detail -->
                                                                    <div class="tab-pane fade" id="inventory-goods-{{ $good->id }}" role="tabpanel">
                                                                        <div class="inventory-content">
                                                                            <h6 class="text-muted mb-3">Thông tin tồn kho</h6>
                                                                            <div class="table-responsive" style="max-width: 500px;">
                                                                                <table class="table table-bordered table-striped align-middle text-center">
                                                                                
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td><strong>Tồn kho hiện tại</strong></td>
                                                                                            <td id="productDetailCurrentStock-goods-{{ $good->id }}">{{ $good->ton_kho ?? 0 }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><strong>Tồn kho tối đa</strong></td>
                                                                                            <td id="productDetailMaxStock-goods-{{ $good->id }}">{{ $good->ton_cao_nhat ?? '999,999,999' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><strong>Tồn kho tối thiểu</strong></td>
                                                                                            <td id="productDetailMinStock-goods-{{ $good->id }}">{{ $good->ton_thap_nhat ?? 0 }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><strong>Định mức tồn</strong></td>
                                                                                            <td id="productDetailStockRange-goods-{{ $good->id }}">{{ $good->ton_thap_nhat ?? 0 }} - {{ $good->ton_cao_nhat ?? '999,999,999' }}</td>
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
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="showDeleteGoodsConfirmation({{ $good->id }}, '{{ $good->ma_hang }}', '{{ $good->ten_hang_hoa }}')">
                                                                                <i class="fas fa-trash"></i> Xóa
                                                                            </button>
                                                                        </div>
                                                                        <div class="right-actions">
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #1db46a; border-color: #1db46a; color: white;" onclick="openEditGoodsModal({{ $good->id }})">
                                                                                <i class="fas fa-edit"></i> Chỉnh sửa
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="printLabel({{ $good->id }})">
                                                                                <i class="fas fa-print"></i> In tem mã
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="openUnitModal({{ $good->id }})">
                                                                                <i class="fas fa-cog"></i> Thiết lập đơn vị tính
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
                                                <br>Chưa có hàng hóa nào
                                            </td>
                                        </tr>
                                    @endforelse

                                    <!-- Hiển thị dịch vụ -->
                                    {{-- @forelse($services ?? [] as $service)
                                        <tr class="product-row service-row" 
                                            data-product-id="service-{{ $service->id }}" 
                                            data-category-id="{{ $service->nhom_hang_id }}"
                                            style="cursor: pointer;" 
                                            onclick="toggleServiceDetail({{ $service->id }}, this)">
                                            <td>
                                                <input type="checkbox" class="form-check-input" onclick="event.stopPropagation()">
                                            </td>
                                            <td>
                                                <div class="product-image-container">
                                                    <img src="{{ $service->image ? asset('storage/' . $service->image) : asset('images/default-service.png') }}"
                                                         alt="{{ $service->ten_dich_vu }}"
                                                         class="img-thumbnail product-image"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td><span class="product-code">{{ $service->ma_hang ?? 'N/A' }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="product-name">{{ $service->ten_dich_vu ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td><span class="product-abbreviation">{{ $service->hinh_thuc == 'tai_nha_thuoc' ? 'Tại NT' : 'Tại nhà' }}</span></td>
                                            <td>{{ $service->formatted_price ?? '0 VND' }}</td>
                                            <td>-</td>
                                            <td>
                                                <span class="badge {{ $service->trang_thai == 'kich_hoat' ? 'bg-success' : ($service->trang_thai == 'tam_ngung' ? 'bg-warning' : 'bg-secondary') }}">
                                                    {{ $service->trang_thai == 'kich_hoat' ? 'Kích hoạt' : ($service->trang_thai == 'tam_ngung' ? 'Tạm ngưng' : 'Lưu tạm') }}
                                                </span>
                                            </td>
                                            <td>{{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <!-- Expandable Detail Row cho dịch vụ -->
                                        <tr class="detail-row" id="detail-row-service-{{ $service->id }}" style="display: none;">
                                            <td colspan="10" class="p-0">
                                                <div class="detail-content">
                                                    <div class="row">
                                                        <!-- Ảnh dịch vụ -->
                                                        <div class="col-md-3">
                                                            <div class="product-image-detail-large">
                                                                <img id="serviceDetailImageLarge-{{ $service->id }}" 
                                                                     src="{{ $service->image ? asset('storage/' . $service->image) : asset('images/default-service.png') }}"
                                                                     alt="Service Image"
                                                                     class="img-fluid rounded"
                                                                     style="width: 100%; height: 200px; object-fit: cover;">
                                                            </div>
                                                        </div>
                                                        <!-- Thông tin dịch vụ -->
                                                        <div class="col-md-9">
                                                            <div class="product-info-detail">
                                                                <!-- Service Header -->
                                                                <div class="product-header mb-4">
                                                                    <h4 class="product-title mb-2">{{ $service->ten_dich_vu ?? 'N/A' }}</h4>
                                                                    <div class="product-category mb-2">
                                                                        <small class="text-muted">Nhóm dịch vụ: {{ $service->category->name ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="product-tags">
                                                                        <span class="badge bg-info me-2">Dịch vụ</span>
                                                                        <span class="badge {{ $service->hinh_thuc == 'tai_nha_thuoc' ? 'bg-primary' : 'bg-secondary' }} me-2">
                                                                            {{ $service->hinh_thuc == 'tai_nha_thuoc' ? 'Tại nhà thuốc' : 'Tại nhà khách' }}
                                                                        </span>
                                                                        <span class="badge {{ $service->trang_thai == 'kich_hoat' ? 'bg-success' : ($service->trang_thai == 'tam_ngung' ? 'bg-warning' : 'bg-secondary') }}">
                                                                            {{ $service->trang_thai == 'kich_hoat' ? 'Kích hoạt' : ($service->trang_thai == 'tam_ngung' ? 'Tạm ngưng' : 'Lưu tạm') }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <!-- Tabs -->
                                                                <ul class="nav nav-tabs" id="serviceDetailTabs-{{ $service->id }}" role="tablist">
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link active" id="info-tab-service-{{ $service->id }}" data-bs-toggle="tab" data-bs-target="#info-service-{{ $service->id }}" type="button" role="tab" style="margin-left:-15px;">Thông tin</button>
                                                                    </li>
                                                                    <li class="" role="presentation">
                                                                        <button class="nav-link" id="description-tab-service-{{ $service->id }}" data-bs-toggle="tab" data-bs-target="#description-service-{{ $service->id }}" type="button" role="tab">Mô tả, ghi chú</button>
                                                                    </li>
                                                                </ul>
                                                                <!-- Tab content -->
                                                                <div class="tab-content mt-3" id="serviceDetailTabContent-{{ $service->id }}">
                                                                    <!-- Tab Thông tin -->
                                                                    <div class="tab-pane fade show active" id="info-service-{{ $service->id }}" role="tabpanel">
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
                                                                                                    <td><strong>Mã dịch vụ</strong></td>
                                                                                                    <td id="serviceDetailCodeLarge-{{ $service->id }}">{{ $service->ma_hang ?? 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Chi phí thực hiện</strong></td>
                                                                                                    <td class="text-success" id="serviceDetailPriceLarge-{{ $service->id }}">{{ $service->formatted_price ?? '0 VND' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Hình thức</strong></td>
                                                                                                    <td id="serviceDetailType-{{ $service->id }}">{{ $service->hinh_thuc == 'tai_nha_thuoc' ? 'Tại nhà thuốc' : 'Tại nhà khách' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Thời gian thực hiện</strong></td>
                                                                                                    <td id="serviceDetailDuration-{{ $service->id }}">{{ $service->thoi_gian_thuc_hien ? $service->thoi_gian_thuc_hien . ' phút' : 'Chưa xác định' }}</td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="info-group">
                                                                                    <h6 class="text-muted mb-3">Thông tin bổ sung</h6>
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
                                                                                                    <td><strong>Nhóm dịch vụ</strong></td>
                                                                                                    <td id="serviceDetailCategory-{{ $service->id }}">{{ $service->category->name ?? 'Chưa phân nhóm' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Ngày tạo</strong></td>
                                                                                                    <td id="serviceDetailCreated-{{ $service->id }}">{{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td><strong>Cập nhật lần cuối</strong></td>
                                                                                                    <td id="serviceDetailUpdated-{{ $service->id }}">{{ $service->updated_at ? $service->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Tab Mô tả -->
                                                                    <div class="tab-pane fade" id="description-service-{{ $service->id }}" role="tabpanel">
                                                                        <div class="description-content">
                                                                            <h6 class="text-muted mb-3">Mô tả dịch vụ</h6>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-bordered table-striped align-middle text-center">
                                                                                    <thead class="table-primary">
                                                                                        <tr>
                                                                                            <th scope="col">Nội dung</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td id="serviceDetailDescriptionLarge-{{ $service->id }}" class="text-start">
                                                                                                {{ $service->mo_ta ?? 'Chưa có mô tả' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                            @if($service->ghi_chu)
                                                                                <h6 class="text-muted mb-3 mt-4">Ghi chú</h6>
                                                                                <div class="table-responsive">
                                                                                    <table class="table table-bordered table-striped align-middle text-center">
                                                                                        <thead class="table-primary">
                                                                                            <tr>
                                                                                                <th scope="col">Ghi chú</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td id="serviceDetailNotesLarge-{{ $service->id }}" class="text-start">
                                                                                                    {{ $service->ghi_chu }}
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Action Buttons -->
                                                                <div class="action-buttons-container">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div class="left-actions">
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="showDeleteServiceConfirmation({{ $service->id }}, '{{ $service->ma_hang }}', '{{ $service->ten_dich_vu }}')">
                                                                                <i class="fas fa-trash"></i> Xóa
                                                                            </button>
                                                                        </div>
                                                                        <div class="right-actions">
                                                                            <button type="button" class="btn btn-sm me-2" style="background-color: #1db46a; border-color: #1db46a; color: white;" onclick="openEditServiceModal({{ $service->id }})">
                                                                                <i class="fas fa-edit"></i> Chỉnh sửa
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
                                    @endforelse --}}
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
                        @foreach($parentCategories ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }} </option>
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
@include('admin.products.Danhsachhanghoa.create.medicine')
@include('admin.products.Danhsachhanghoa.create.goods')
@include('admin.products.Danhsachhanghoa.create.service')
@include('admin.products.Danhsachhanghoa.edit.medicine')
@include('admin.products.Danhsachhanghoa.edit.goods')
@include('admin.products.Danhsachhanghoa.edit.service')
@include('admin.products.Danhsachhanghoa.formmodal.unit_modal')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/management/medicine-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/reponsive.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/products/hanghoa/medicine-management.js') }}"></script>
    <script src="{{ asset('js/products/hanghoa/goods-management.js') }}"></script>
    <script src="{{ asset('js/products/hanghoa/service-management.js') }}"></script>
    <script src="{{ asset('js/products/hanghoa/search-filter.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
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

<!-- Hidden form for delete -->
<form id="deleteMedicineForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" id="deleteMedicineId" name="medicine_id">
</form>

<script>
// toggleServiceDetail function is now in service-management.js

/**
 * Show delete service confirmation
 */
function showDeleteServiceConfirmation(serviceId, serviceCode, serviceName) {
    // Update modal content
    document.getElementById('deleteMedicineCode').textContent = serviceCode;
    document.getElementById('deleteMedicineName').textContent = serviceName;
    document.getElementById('deleteMedicineId').value = serviceId;
    
    // Update form action for service
    const form = document.getElementById('deleteMedicineForm');
    form.action = `/admin/services/${serviceId}`;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    modal.show();
}

/**
 * View service detail in modal
 */
function viewServiceDetail(serviceId) {
    // Use the function from service-management.js
    if (typeof viewServiceDetail !== 'undefined') {
        viewServiceDetail(serviceId);
    } else {
        alert('Chi tiết dịch vụ sẽ được hiển thị ở đây');
    }
}


/**
 * Search products to include services
 */
function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.product-row');
    
    rows.forEach(row => {
        const productCode = row.querySelector('.product-code')?.textContent.toLowerCase() || '';
        const productName = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
        
        const matches = productCode.includes(searchTerm) || productName.includes(searchTerm);
        
        row.style.display = matches ? 'table-row' : 'none';
        
        // Also hide detail row when searching
        const detailRow = document.getElementById(row.onclick?.toString().match(/detail-row-[^']+/)?.[0]);
        if (detailRow) {
            detailRow.style.display = 'none';
        }
    });
}

// Category dropdown functionality
let categoriesData = [];
let selectedCategoryId = null;

// Load categories data
function loadCategories() {
    const container = document.getElementById('categoriesListContainer');
    if (!container) return;
    
    // Show loading
    container.innerHTML = `
        <div class="category-loading text-center py-3">
            <i class="fas fa-spinner fa-spin"></i>
            <small class="text-muted">Đang tải...</small>
        </div>
    `;
    
    // Load categories from API to get proper tree structure
    fetch('/admin/categories/modal/data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                categoriesData = data.data;
                renderCategories();
            } else {
                container.innerHTML = `
                    <div class="text-center py-3 text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <small>Lỗi tải danh mục</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            container.innerHTML = `
                <div class="text-center py-3 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <small>Lỗi tải danh mục</small>
                </div>
            `;
        });
}

// Render categories in dropdown with proper hierarchy
function renderCategories() {
    const container = document.getElementById('categoriesListContainer');
    if (!container) return;
    
    if (categoriesData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-3 text-muted">
                <i class="fas fa-folder-open"></i>
                <small>Không có nhóm hàng nào</small>
            </div>
        `;
        return;
    }
    
    container.innerHTML = '';
    
    // Render each root category and its children
    categoriesData.forEach(category => {
        renderCategoryItem(category, container, 0);
    });
}

// Render individual category item with hierarchy
function renderCategoryItem(category, container, level) {
    const item = document.createElement('div');
    item.className = `category-tree-item level-${level}`;
    item.dataset.categoryId = category.id;
    
    // Add proper indentation based on level
    const prefix = level > 0 ? '- '.repeat(level) : '';
    
    item.innerHTML = `
        <span class="category-tree-name">${prefix}${category.name}</span>
        <button class="category-tree-edit" onclick="editCategory(${category.id}, '${category.name}', event)" title="Chỉnh sửa">
            <i class="fas fa-pen"></i>
        </button>
    `;
    
    // Click to select category
    item.addEventListener('click', (e) => {
        if (!e.target.closest('.category-tree-edit')) {
            selectCategory(category.id, category.name);
            filterProducts();
        }
    });
    
    container.appendChild(item);
    
    // Render children recursively
    if (category.children && category.children.length > 0) {
        category.children.forEach(child => {
            renderCategoryItem(child, container, level + 1);
        });
    }
}

// Toggle category dropdown
function toggleCategoryDropdown() {
    const container = document.getElementById('categoriesListContainer');
    const icon = document.getElementById('categoryDropdownIcon');
    const dropdown = document.querySelector('.category-dropdown-container');
    
    if (!container || !icon || !dropdown) return;
    
    const isOpen = container.style.display !== 'none';
    
    if (isOpen) {
        container.style.display = 'none';
        dropdown.classList.remove('open');
    } else {
        container.style.display = 'block';
        dropdown.classList.add('open');
        
        // Load categories if not loaded yet
        if (categoriesData.length === 0) {
            loadCategories();
        }
    }
}

// Select category
function selectCategory(categoryId, categoryName) {
    selectedCategoryId = categoryId;
    
    // Update display
    document.getElementById('selectedCategoryText').textContent = categoryName;
    document.querySelector('.btn-reset-category').style.display = 'inline-block';
    
    // Close dropdown
    const container = document.getElementById('categoriesListContainer');
    const dropdown = document.querySelector('.category-dropdown-container');
    if (container) container.style.display = 'none';
    if (dropdown) dropdown.classList.remove('open');
    
    // Update visual state
    document.querySelectorAll('.category-tree-item').forEach(item => {
        item.classList.remove('selected');
        if (parseInt(item.dataset.categoryId) === categoryId) {
            item.classList.add('selected');
        }
    });
    
    // Trigger filter
    filterProducts();
}

// Reset category selection
function resetCategorySelection(event) {
    event.stopPropagation();
    
    selectedCategoryId = null;
    document.getElementById('selectedCategoryText').textContent = 'Chọn nhóm hàng';
    document.querySelector('.btn-reset-category').style.display = 'none';
    
    // Remove selection visual state
    document.querySelectorAll('.category-tree-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Trigger filter
    filterProducts();
}

// Edit category
function editCategory(categoryId, categoryName, event) {
    event.stopPropagation();
    
    const modal = document.getElementById('createCategoryModal');
    if (!modal) return;
    
    // Set form data
    const nameInput = modal.querySelector('#category-name');
    const parentSelect = modal.querySelector('#parent-category');
    const title = modal.querySelector('.modal-title');
    
    if (nameInput) nameInput.value = categoryName;
    if (parentSelect) parentSelect.value = '';
    if (title) title.textContent = 'Chỉnh sửa nhóm hàng';
    
    // Update form action for edit
    const form = modal.querySelector('form');
    if (form) {
        form.action = `/admin/categories/${categoryId}`;
        // Add method override for PUT
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
    }
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.category-dropdown-container');
    if (dropdown && !dropdown.contains(event.target)) {
        const container = document.getElementById('categoriesListContainer');
        if (container) container.style.display = 'none';
        if (dropdown) dropdown.classList.remove('open');
    }
});

// Filter products function (updated to work with sidebar categories)
function filterProducts() {
    // Get selected category
    const categoryId = selectedCategoryId;
    
    // Get other filter values
    const manufacturerId = document.querySelector('select[name="manufacturer_id"]')?.value || '';
    const positionId = document.querySelector('select[name="position_id"]')?.value || '';
    const productType = document.querySelector('select[name="product_type"]')?.value || '';
    
    // Filter table rows
    const rows = document.querySelectorAll('.product-row');
    
    rows.forEach(row => {
        let showRow = true;
        
        // Filter by category
        if (categoryId && row.dataset.categoryId !== categoryId.toString()) {
            showRow = false;
        }
        
        // Filter by manufacturer
        if (manufacturerId && row.dataset.manufacturerId !== manufacturerId) {
            showRow = false;
        }
        
        // Filter by position
        if (positionId && row.dataset.positionId !== positionId) {
            showRow = false;
        }
        
        // Filter by product type
        if (productType) {
            if (productType === 'medicine' && !row.classList.contains('medicine-row')) {
                showRow = false;
            } else if (productType === 'goods' && !row.classList.contains('goods-row')) {
                showRow = false;
            } else if (productType === 'service' && !row.classList.contains('service-row')) {
                showRow = false;
            }
        }
        
        // Show/hide row
        row.style.display = showRow ? 'table-row' : 'none';
        
        // Hide detail rows when filtering
        const detailRow = document.querySelector(`#detail-row-${row.dataset.productId}`);
        if (detailRow) {
            detailRow.style.display = 'none';
        }
    });
}
</script>

{{-- Categories are now displayed directly in sidebar --}}

@endsection