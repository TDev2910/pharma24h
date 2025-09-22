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
                                        <!-- Expandable Detail Row cho thuốc với layout mới -->
                                        <tr class="detail-row" id="detail-row-{{ $medicine->id }}" style="display: none;">
                                            <td colspan="10" class="p-0">
                                                <div class="product-detail">
                                                    <!-- Header -->
                                                    <div class="pd-header">
                                                        <div class="pd-thumb">
                                                            <img src="{{ $medicine->image_url }}" alt="{{ $medicine->ten_thuoc }}">
                                                            <div class="pd-thumb-fallback">Ảnh</div>
                                                        </div>
                                                        <div class="pd-meta">
                                                            <h2 class="pd-title">{{ $medicine->ten_thuoc ?? 'N/A' }}</h2>
                                                            <div class="pd-subtitle">Nhóm hàng: {{ $medicine->category->name ?? 'N/A' }}</div>

                                                            <div class="pd-badges">
                                                                <span class="badge badge-blue">Thuốc</span>
                                                                <span class="badge badge-green">Bán trực tiếp</span>
                                                                <span class="badge badge-orange">Không tích điểm</span>
                                                            </div>

                                                            <div class="pd-tabs">
                                                                <button class="tab active" onclick="switchTab({{ $medicine->id }}, 'info')">Thông tin</button>
                                                                <button class="tab" onclick="switchTab({{ $medicine->id }}, 'description')">Mô tả, ghi chú</button>
                                                                <button class="tab" onclick="switchTab({{ $medicine->id }}, 'inventory')">Tồn kho</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Tab Content -->
                                                    <!-- Tab Thông tin -->
                                                    <div class="tab-content" id="info-{{ $medicine->id }}" style="display: block;">
                                                        <div class="pd-body">
                                                            <section class="info-card">
                                                                <header class="card-header">Thông tin chung</header>
                                                                <div class="card-body">
                                                                    <table class="product-table">
                                                                        <tbody>
                                                                            <tr><td class="label">Mã hàng</td><td class="value">{{ $medicine->ma_hang ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Mã vạch</td><td class="value">{{ $medicine->ma_vach ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Giá vốn</td><td class="value">{{ $medicine->gia_von_formatted ?? '0 VND' }}</td></tr>
                                                                            <tr><td class="label">Giá bán</td><td class="value price">{{ $medicine->gia_ban_formatted ?? '0 VND' }}</td></tr>
                                                                            <tr><td class="label">Tên viết tắt</td><td class="value">{{ $medicine->ten_viet_tat ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Vị trí</td><td class="value">{{ $medicine->position->name ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Định mức tồn</td><td class="value">{{ $medicine->ton_thap_nhat ?? 0 }} - {{ $medicine->ton_cao_nhat ?? '999,999,999' }}</td></tr>
                                                                            <tr><td class="label">Trọng lượng</td><td class="value">{{ $medicine->trong_luong ? $medicine->trong_luong . ' g' : 'N/A' }}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </section>

                                                            <section class="info-card">
                                                                <header class="card-header">Thông tin thuốc</header>
                                                                <div class="card-body">
                                                                    <table class="product-table">
                                                                        <tbody>
                                                                            <tr><td class="label">Mã hàng</td><td class="value">{{ $medicine->ma_hang ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Hoạt chất</td><td class="value">{{ $medicine->hoat_chat ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Đường dùng</td><td class="value">{{ $medicine->drugRoute->name ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Số đăng ký</td><td class="value">{{ $medicine->so_dang_ky ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Hàm lượng</td><td class="value">{{ $medicine->ham_luong ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Quy cách đóng gói</td><td class="value">{{ $medicine->quy_cach_dong_goi ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Hãng sản xuất</td><td class="value">{{ $medicine->manufacturer->name ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Nước sản xuất</td><td class="value">{{ $medicine->nuoc_san_xuat ?? 'Việt Nam' }}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>  
                                                            </section>
                                                        </div>
                                                    </div>

                                                    <!-- Tab Mô tả, ghi chú -->
                                                    <div class="tab-content" id="description-{{ $medicine->id }}" style="display: none;">
                                                        <div class="description-content">
                                                            <section class="info-card">
                                                                <header class="card-header">Mô tả sản phẩm</header>
                                                                <div class="card-body">
                                                                    <div class="description-text p-3">
                                                                        {{ $medicine->mo_ta ?? 'Chưa có mô tả' }}
                                                                    </div>
                                                                </div>
                                                            </section>
                                                            @if($medicine->ghi_chu)
                                                            <section class="info-card mt-3">
                                                                <header class="card-header">Ghi chú</header>
                                                                <div class="card-body">
                                                                    <div class="description-text p-3">
                                                                        {{ $medicine->ghi_chu }}
                                                                    </div>
                                                                </div>
                                                            </section>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Tab Tồn kho -->
                                                    <div class="tab-content" id="inventory-{{ $medicine->id }}" style="display: none;">
                                                        <div class="inventory-content">
                                                            <section class="info-card">
                                                                <header class="card-header">Thông tin tồn kho</header>
                                                                <div class="card-body">
                                                                    <table class="product-table">
                                                                        <tbody>
                                                                            <tr><td class="label">Tồn kho hiện tại</td><td class="value">{{ $medicine->ton_kho ?? 0 }}</td></tr>
                                                                            <tr><td class="label">Tồn kho tối đa</td><td class="value">{{ $medicine->ton_cao_nhat ?? '999,999,999' }}</td></tr>
                                                                            <tr><td class="label">Tồn kho tối thiểu</td><td class="value">{{ $medicine->ton_thap_nhat ?? 0 }}</td></tr>
                                                                            <tr><td class="label">Định mức tồn</td><td class="value">{{ $medicine->ton_thap_nhat ?? 0 }} - {{ $medicine->ton_cao_nhat ?? '999,999,999' }}</td></tr>
                                                                            <tr><td class="label">Trạng thái tồn kho</td><td class="value">
                                                                                @if(($medicine->ton_kho ?? 0) <= ($medicine->ton_thap_nhat ?? 0))
                                                                                    <span class="text-danger">Sắp hết hàng</span>
                                                                                @elseif(($medicine->ton_kho ?? 0) >= ($medicine->ton_cao_nhat ?? 999999999))
                                                                                    <span class="text-warning">Tồn kho cao</span>
                                                                                @else
                                                                                    <span class="text-success">Bình thường</span>
                                                                                @endif
                                                                            </td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Action Buttons -->
                                                    <div class="action-buttons-container mt-4">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="left-actions">
                                                                <button type="button" class="btn btn-sm me-2" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d;" onclick="showDeleteMedicineConfirmation({{ $medicine->id }}, '{{ $medicine->ma_hang }}', '{{ $medicine->ten_thuoc }}')">
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
                                    @forelse($goods ?? [] as $good)
                                        <tr class="product-row goods-row" 
                                            data-product-id="{{ $good->id }}" 
                                            data-category-id="{{ $good->nhom_hang_id }}"
                                            data-manufacturer-id="{{ $good->manufacturer_id }}"
                                            data-position-id="{{ $good->position_id }}"
                                            style="cursor: pointer;" 
                                            onclick="toggleProductDetail({{ $good->id }}, this)">
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
                                            <td>{{ $good->gia_ban_formatted }}</td>
                                            <td>{{ $good->gia_von_formatted }}</td>
                                        <td>{{ $good->ton_thap_nhat ?? 0 }}</td>
                                        <td>{{ $good->created_at ? $good->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <!-- Expandable Detail Row cho hàng hóa với layout mới -->
                                        <tr class="detail-row" id="detail-row-{{ $good->id }}" style="display: none;">
                                            <td colspan="10" class="p-0">
                                                <div class="product-detail">
                                                    <!-- Header -->
                                                    <div class="pd-header">
                                                        <div class="pd-thumb">
                                                            <img src="{{ $good->image_url }}" alt="{{ $good->ten_hang_hoa }}">
                                                            <div class="pd-thumb-fallback">Ảnh</div>
                                                        </div>
                                                        <div class="pd-meta">
                                                            <h2 class="pd-title">{{ $good->ten_hang_hoa ?? 'N/A' }}</h2>
                                                            <div class="pd-subtitle">Nhóm hàng: {{ $good->category->name ?? 'N/A' }}</div>

                                                            <div class="pd-badges">
                                                                <span class="badge badge-blue">Hàng hóa</span>
                                                                <span class="badge badge-green">{{ $good->ban_truc_tiep ? 'Bán trực tiếp' : 'Không bán trực tiếp' }}</span>
                                                                <span class="badge badge-orange">Không tích điểm</span>
                                                            </div>

                                                            <div class="pd-tabs">
                                                                <button class="tab active" onclick="switchTab({{ $good->id }}, 'info')">Thông tin</button>
                                                                <button class="tab" onclick="switchTab({{ $good->id }}, 'description')">Mô tả, ghi chú</button>
                                                                <button class="tab" onclick="switchTab({{ $good->id }}, 'inventory')">Tồn kho</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Tab Content -->
                                                    <!-- Tab Thông tin -->
                                                    <div class="tab-content" id="info-{{ $good->id }}" style="display: block;">
                                                        <div class="pd-body">
                                                            <section class="info-card">
                                                                <header class="card-header">Thông tin chung</header>
                                                                <div class="card-body">
                                                                    <table class="product-table">
                                                                        <tbody>
                                                                            <tr><td class="label">Mã hàng</td><td class="value">{{ $good->ma_hang ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Mã vạch</td><td class="value">{{ $good->ma_vach ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Giá vốn</td><td class="value">{{ $good->gia_von_formatted ?? '0 VND' }}</td></tr>
                                                                            <tr><td class="label">Giá bán</td><td class="value price">{{ $good->gia_ban_formatted ?? '0 VND' }}</td></tr>
                                                                            <tr><td class="label">Tên viết tắt</td><td class="value">{{ $good->ten_viet_tat ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Vị trí</td><td class="value">{{ $good->position->name ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Định mức tồn</td><td class="value">{{ $good->ton_thap_nhat ?? 0 }} - {{ $good->ton_cao_nhat ?? '999,999,999' }}</td></tr>
                                                                            <tr><td class="label">Trọng lượng</td><td class="value">{{ $good->trong_luong ? $good->trong_luong . ' g' : 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Đơn vị tính</td><td class="value">{{ $good->don_vi_tinh ?? 'N/A' }}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </section>

                                                            <section class="info-card">
                                                                <header class="card-header">Thông tin hàng hóa</header>
                                                                <div class="card-body">
                                                                    <table class="product-table">
                                                                        <tbody>
                                                                            <tr><td class="label">Mã hàng</td><td class="value">{{ $good->ma_hang ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Mã vạch</td><td class="value">{{ $good->ma_vach ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Trọng lượng</td><td class="value">{{ $good->trong_luong ? $good->trong_luong . ' g' : 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Đơn vị tính</td><td class="value">{{ $good->don_vi_tinh ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Quản lý theo lô</td><td class="value">{{ $good->quan_ly_theo_lo ? 'Có' : 'Không' }}</td></tr>
                                                                            <tr><td class="label">Quy cách đóng gói</td><td class="value">{{ $good->quy_cach_dong_goi ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Hãng sản xuất</td><td class="value">{{ $good->manufacturer->name ?? 'N/A' }}</td></tr>
                                                                            <tr><td class="label">Nước sản xuất</td><td class="value">{{ $good->nuoc_san_xuat ?? 'Việt Nam' }}</td></tr>
                                                                            <tr><td class="label">Bán trực tiếp</td><td class="value">{{ $good->ban_truc_tiep ? 'Có' : 'Không' }}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>

                                                    <!-- Tab Mô tả, ghi chú -->
                                                    <div class="tab-content" id="description-{{ $good->id }}" style="display: none;">
                                                        <div class="description-content">
                                                            <section class="info-card">
                                                                <header class="card-header">Mô tả sản phẩm</header>
                                                                <div class="card-body">
                                                                    <div class="description-text p-3">
                                                                        {{ $good->mo_ta ?? 'Chưa có mô tả' }}
                                                                    </div>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>

                                                    <!-- Tab Tồn kho -->
                                                    <div class="tab-content" id="inventory-{{ $good->id }}" style="display: none;">
                                                        <div class="inventory-content">
                                                            <section class="info-card">
                                                                <header class="card-header">Thông tin tồn kho</header>
                                                                <div class="card-body">
                                                                    <table class="product-table">
                                                                        <tbody>
                                                                            <tr><td class="label">Tồn kho hiện tại</td><td class="value">{{ $good->ton_kho ?? 0 }}</td></tr>
                                                                            <tr><td class="label">Tồn kho tối đa</td><td class="value">{{ $good->ton_cao_nhat ?? '999,999,999' }}</td></tr>
                                                                            <tr><td class="label">Tồn kho tối thiểu</td><td class="value">{{ $good->ton_thap_nhat ?? 0 }}</td></tr>
                                                                            <tr><td class="label">Định mức tồn</td><td class="value">{{ $good->ton_thap_nhat ?? 0 }} - {{ $good->ton_cao_nhat ?? '999,999,999' }}</td></tr>
                                                                            <tr><td class="label">Trạng thái tồn kho</td><td class="value">
                                                                                @if(($good->ton_kho ?? 0) <= ($good->ton_thap_nhat ?? 0))
                                                                                    <span class="text-danger">Sắp hết hàng</span>
                                                                                @elseif(($good->ton_kho ?? 0) >= ($good->ton_cao_nhat ?? 999999999))
                                                                                    <span class="text-warning">Tồn kho cao</span>
                                                                                @else
                                                                                    <span class="text-success">Bình thường</span>
                                                                                @endif
                                                                            </td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Action Buttons -->
                                                    <div class="action-buttons-container mt-4">
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

<!-- Modal chỉnh sửa nhóm hàng -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editCategoryForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Chỉnh sửa nhóm hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="edit-category-name" class="form-label">Tên nhóm hàng</label>
                    <input type="text" class="form-control" id="edit-category-name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="edit-parent-category" class="form-label">Nhóm cha (nếu có)</label>
                    <select class="form-select" id="edit-parent-category" name="parent_id">
                        <option value="">Không có nhóm cha</option>
                        @foreach($parentCategories ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="showDeleteConfirm()" style="margin-right: 260px;">Xóa</button>
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


{{-- thêm file css --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/reponsive.css') }}">
<link rel="stylesheet" href="{{ asset('css/Hanghoa/Danhsachhanghoa/medicine.css') }}">
<link rel="stylesheet" href="{{ asset('css/Hanghoa/Danhsachhanghoa/goods.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/products/hanghoa/medicine-management.js') }}"></script>
    <script src="{{ asset('js/products/hanghoa/goods-management.js') }}"></script>
    <script src="{{ asset('js/products/hanghoa/service-management.js') }}"></script>
    <script src="{{ asset('js/products/hanghoa/search-filter.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
@endpush


<script>
// toggleServiceDetail function is now in service-management.js


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
    
    const modal = document.getElementById('editCategoryModal');
    if (!modal) return;
    
    // Set form data
    const nameInput = modal.querySelector('#edit-category-name');
    const parentSelect = modal.querySelector('#edit-parent-category');
    
    if (nameInput) nameInput.value = categoryName;
    if (parentSelect) parentSelect.value = '';
    
    // Update form action for edit
    const form = modal.querySelector('#editCategoryForm');
    if (form) {
        form.action = `/admin/categories/${categoryId}`;
        form.setAttribute('data-category-id', categoryId);
    }
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// Show delete confirmation from edit modal
function showDeleteConfirm() {
    const editModal = document.getElementById('editCategoryModal');
    const categoryName = document.getElementById('edit-category-name').value;
    const categoryId = document.getElementById('editCategoryForm').getAttribute('data-category-id');
    
    if (!categoryName) {
        alert('Vui lòng nhập tên nhóm hàng!');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn xóa nhóm hàng "${categoryName}"?`)) {
        // Tạo form để submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/categories/${categoryId}`;
        
        // Thêm CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Thêm method DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
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