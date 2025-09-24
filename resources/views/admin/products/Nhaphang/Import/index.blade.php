@extends('layouts.admin')

@section('title', 'Quản lý nhập hàng')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Title Section -->
            <div class="title-section">
                <h4 class="header-title mb-0">Nhập hàng</h4>
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Theo mã phiếu nhập, nhà cung cấp">
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="action-buttons d-flex align-items-center gap-2">
                    <!-- Tạo phiếu nhập mới -->
                    <button class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i>
                        Nhập hàng
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
            <aside class="col-xl-3 col-lg-3 col-md-4 mb-4">
                <div class="card sidebar-filter border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="filter-section mb-3">
                            <div class="section-title" style="margin-bottom: 15px;">Trạng thái</div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="statusImported" name="status[]" value="imported">
                                <label class="form-check-label" for="statusImported">Đã nhập hàng</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="statusCancelled" name="status[]" value="cancelled">
                                <label class="form-check-label" for="statusCancelled">Đã hủy</label>
                            </div>
                        </div>
                        <div class="filter-section">
                            <div class="section-title" style="margin-bottom: 15px;">Thời gian</div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <input type="date" class="form-control form-control-sm" name="from_date" placeholder="Từ ngày">
                                </div>
                                <div class="col-12">
                                    <input type="date" class="form-control form-control-sm" name="to_date" placeholder="Đến ngày">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- Right Main Content -->
            <div class="col-xl-9 col-lg-9 col-md-8">
                <div class="main-content">
                    <!-- Table Section -->
                    <div class="p-2" style="background: #ffffff; border-radius: 8px; border: 1px solid #dee2e6;">
                        <div class="table-section">
                            <table class="table import-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input">
                                        </th>
                                        <th>Mã nhập hàng</th>
                                        <th>Thời gian</th>
                                        <th>Mã NCC</th>
                                        <th>Nhà cung cấp</th>
                                        <th>Cần trả NCC</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <br>Không có phiếu nhập hàng nào
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Summary & Pagination -->
                    <div class="summary-section">
                        <div class="row">
                            <div class="col-md-6">
                                 <small class="text-muted">
                                     Tổng cộng: <strong>0</strong> phiếu nhập
                                 </small>
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

<!-- Modal Tạo Phiếu Nhập -->
<div class="modal fade" id="createImportModal" tabindex="-1" aria-labelledby="createImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createImportModalLabel">
                    <i class="fas fa-plus me-2"></i>Tạo phiếu nhập hàng mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createImportForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                                <select class="form-select" name="supplier_id" required>
                                    <option value="">Chọn nhà cung cấp</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày nhập</label>
                                <input type="date" class="form-control" name="import_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Ghi chú về phiếu nhập hàng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Tạo phiếu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    

@push('styles')
<link href="{{ asset('css/Hanghoa/Nhaphang/import.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>
// Basic JavaScript functions for import management
function searchImports() {
    console.log('Search function placeholder');
}

function filterImports() {
    console.log('Filter function placeholder');
}
</script>
@endpush

@endsection 