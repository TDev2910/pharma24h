@extends('layouts.admin')

@section('title', 'Quản lý hàng hóa')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="sidebar sidebar-filter">
                <h5 class="mb-4">Hàng Hóa</h5>
                <div class="filter-section">
                    <label>
                        Nhóm hàng
                        <!-- mo modal cung trang -->
                        <a href="#" class="create-link" data-bs-toggle="modal" data-bs-target="#createCategoryModal" style="right: 50px;">Tạo mới</a>
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm flex-grow-1" name="category_id">
                            <option value="">Chọn nhóm hàng</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
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
        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="main-content">
                <!-- Header -->
                <div class="search-section">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="col-10">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Theo mã, tên hàng">
                            </div>
                        </div>      
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <div class="dropdown">
                                    <button class="btn btn-success-custom btn-sm dropdown-toggle" type="button" id="createDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-plus"></i> Tạo mới
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="createDropdown">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createMedicineModal">Thuốc</a></li>
                                        <li><a class="dropdown-item" href="#">Hàng hóa</a></li>
                                        <li><a class="dropdown-item" href="#">Dịch vụ</a></li>
                                        <li><a class="dropdown-item" href="#">Combo - đóng gói</a></li>
                                    </ul>
                                </div>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-download"></i> Import file
                                </button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-upload"></i> Xuất file
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-section">
                    <table class="table product-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input">
                                </th>
                                <th>Mã hàng</th>
                                <th style="min-width: 250px;">Tên hàng</th>
                                <th>Giá bán</th>
                                <th>Giá vốn</th>
                                <th>Tồn kho</th>    
                                <th>Thời gian tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input">
                                </td>
                                <td><span class="product-code">NT00038</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="product-name">Anaferon Tăng Sức Đề Kháng Nga Siro</span>
                                    </div>
                                </td>
                                <td></td>
                                <td>0</td>
                                <td>0</td>
                                <td><span class="stock-badge">0</span></td>  
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Summary & Pagination -->
                <div class="mt-4 pt-3 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- <small class="text-muted">Tổng cộng: <strong>464</strong> sản phẩm | Hiển thị: <strong>0</strong> - <strong>---</strong></small> --}}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item disabled">
                                        <span class="page-link">Previous</span>
                                    </li>
                                    <li class="page-item active">
                                        <span class="page-link">1</span>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Next</a>
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
            @foreach($parents as $cat)
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

@include('admin.products.Danhsachhanghoa.create_medicine')
@endsection

@push('styles')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            height: fit-content;
        }
        .main-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .filter-section { margin-bottom: 20px; }
        .filter-section label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        .create-link {
            color: #20c997;
            text-decoration: none;
            font-weight: 500;
        }
        .create-link:hover { color: #17a085; }
        .btn-success-custom {
            background-color: #20c997;
            border-color: #20c997;
            color: white;
        }
        .btn-success-custom:hover {
            background-color: #17a085;
            border-color: #17a085;
        }
        .search-section {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .table-section { overflow-x: auto; }
        .product-table { margin-bottom: 0; }
        .product-table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
            vertical-align: middle;
            white-space: nowrap;
        }
        .product-table td {
            vertical-align: middle;
            border-color: #e9ecef;
        }
        .product-code { color: #6c757d; font-weight: 500; }
        .product-name { color: #495057; font-weight: 500; }
        .stock-badge {
            background-color: #e9ecef;
            color: #6c757d;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .stock-available {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .time-stamp { color: #6c757d; font-size: 0.875rem; }
        .radio-group { display: flex; flex-direction: column; gap: 8px; }
        .radio-item { display: flex; align-items: center; gap: 8px; }
        .section-divider {
            border-right: 1px solid #dee2e6;
            padding-right: 15px;
        }
        .header-summary {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 0.875rem;
            color: #6c757d;
            text-align: center;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .section-divider {
                border-right: none;
                border-bottom: 1px solid #dee2e6;
                padding-right: 0;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
@endpush

