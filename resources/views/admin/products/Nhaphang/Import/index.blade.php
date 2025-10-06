@extends('layouts.admin')

@section('title', 'Quản lý nhập hàng')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: 0 auto;">    <!-- Header Control Bar -->
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
                    <a href="{{ route('admin.import.create') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i>Nhập hàng
                    </a>
                    
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
            <aside class="col-xl-2 col-lg-2 col-md-4 mb-4">
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
            <div class="col-xl-10 col-lg-10 col-md-8">
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
                                    @forelse($imports as $import)
                                        <tr class="import-row"
                                            data-import-id="{{ $import->id }}"
                                            data-supplier-id="{{ $import->supplier?->id }}"
                                            data-status="{{ $import->status }}"
                                            onclick="toggleImportDetail({{ $import->id }}, this)"
                                            style="cursor:pointer;">
                                        <td>
                                            <input type="checkbox" class="form-check-input">
                                        </td>
                                        <td><span class="import-code">{{ $import->import_code }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($import->import_date ?? $import->created_at)->format('d/m/Y H:i') }}</td>
                                        <td><span class="supplier-code">{{ $import->supplier?->ma_nha_cung_cap }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                            <span class="supplier-name">{{ $import->supplier?->ten_nha_cung_cap }}</span>
                                            </div>
                                        </td>
                                        <td>{{ number_format($import->remaining_amount ?? ($import->total_amount - ($import->paid_amount ?? 0)), 0, ',', '.') }}</td>
                                        <td>
                                            @php $st = $import->status; @endphp
                                            @if ($st === 'imported' || $st === 'completed')
                                            <span class="badge bg-success">Đã nhập hàng</span>
                                            @else
                                            <span class="badge bg-secondary">{{ $st }}</span>
                                            @endif
                                        </td>
                                        </tr>
                                        <!-- Chi tiết phiếu (ẩn mặc định) -->
                                        <tr id="detail-row-{{ $import->id }}" class="detail-row" style="display: none;">
                                            <td colspan="7" class="p-0">
                                                <div class="supplier-detail-container bg-light border-top">
                                                    <div class="tab-container">
                                                        <div class="title-detail">
                                                            <button class="tab active" type="button" onclick="switchTab({{ $import->id }}, 'info', this)">Thông tin</button>
                                                            <button class="tab" type="button" onclick="switchTab({{ $import->id }}, 'history', this)">Lịch sử nhập hàng</button>
                                                        </div>
                                                        
                                                        <!-- Tab Content: Thông tin -->
                                                        <div id="tab-info-{{ $import->id }}" class="tab-content" style="display: block;">
                                                            <div class="import-info-container">
                                                                <!-- Header với mã phiếu và trạng thái -->
                                                                <div class="import-info-header">
                                                                    <h3 class="import-info-code">{{ $import->import_code ?? 'PN000049' }}</h3>
                                                                    <span class="import-info-badge">Đã nhập hàng</span>
                                                                </div>
                                                                
                                                                <!-- Thông tin chi tiết - Layout 2 cột -->
                                                                <div class="import-info-details">
                                                                    <div class="import-info-left">
                                                                        <div class="import-info-item">
                                                                            <span class="import-info-label">Người tạo:</span>
                                                                            <span class="import-info-text">admin</span>
                                                                        </div>
                                                                        
                                                                        <div class="import-info-item">
                                                                            <span class="import-info-label">Tên NCC:</span>
                                                                            <a href="#" class="import-info-link">{{ $import->supplier?->ten_nha_cung_cap ?? 'Công ty TNHH Citigo' }}</a>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="import-info-right">
                                                                        <div class="import-info-item">
                                                                            <span class="import-info-label">Người nhập:</span>
                                                                            <select class="import-info-select">
                                                                                <option value="Admin">Admin</option>
                                                                            </select>
                                                                            <div class="import-info-item">
                                                                                <span class="import-info-label">Ngày nhập:</span>
                                                                                <input type="text" class="import-info-input" value="{{ \Carbon\Carbon::parse($import->import_date ?? $import->created_at)->format('d/m/Y H:i') }}" readonly>
                                                                            </div>   
                                                                        </div>                           
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Phần quản lý sản phẩm -->
                                                            <div class="import-product-section">                                                                                          
                                                                <!-- Bảng danh sách sản phẩm -->
                                                                <div class="import-product-table-container">
                                                                    <table class="import-product-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="import-col-code" style="width: 120px; text-align: center;">Mã hàng</th>
                                                                                <th class="import-col-name" style="width: auto; min-width: 250px;text-align: center;">Tên hàng</th>
                                                                                <th class="import-col-qty" style="text-align: center;">Số lượng</th>
                                                                                <th class="import-col-price" style="width: 120px; text-align: right;">Đơn giá</th>
                                                                                <th class="import-col-discount" style="width: 120px; text-align: right;">Giảm giá</th>
                                                                                <th class="import-col-import-price" style="width: 120px; text-align: right;">Giá nhập</th>
                                                                                <th class="import-col-total">Thành tiền</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($import->items as $item)
                                                                            <tr>
                                                                                <td class="import-col-code">
                                                                                    <a href="#" class="import-product-link">
                                                                                        {{-- Debug: {{ $item->product_type }} - {{ $item->product_id }} --}}
                                                                                        @if(isset($item->product) && $item->product)
                                                                                            @if($item->product_type === 'medicine')
                                                                                                {{ $item->product->ma_hang ?? 'N/A' }}
                                                                                            @elseif($item->product_type === 'goods')
                                                                                                {{ $item->product->ma_hang ?? 'N/A' }}
                                                                                            @else
                                                                                                {{ $item->product->ma_dich_vu ?? 'N/A' }}
                                                                                            @endif
                                                                                        @else
                                                                                            {{ $item->product_id ?? 'N/A' }}
                                                                                        @endif
                                                                                    </a>
                                                                                </td>
                                                                                <td class="import-col-name">
                                                                                    @if(isset($item->product) && $item->product)
                                                                                        @if($item->product_type === 'medicine')
                                                                                            {{ $item->product->ten_thuoc ?? 'N/A' }}
                                                                                        @elseif($item->product_type === 'goods')
                                                                                            {{ $item->product->ten_hang_hoa ?? 'N/A' }}
                                                                                        @else
                                                                                            {{ $item->product->ten_dich_vu ?? 'N/A' }}
                                                                                        @endif
                                                                                    @else
                                                                                        Product not loaded
                                                                                    @endif
                                                                                </td>
                                                                                <td class="import-col-qty">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                                                                <td class="import-col-price">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                                                <td class="import-col-discount">{{ number_format($item->discount, 0, ',', '.') }}</td>
                                                                                <td class="import-col-import-price">{{ number_format($item->unit_price - $item->discount, 0, ',', '.') }}</td>
                                                                                <td class="import-col-total">{{ number_format($item->total_price, 0, ',', '.') }}</td>
                                                                        </tr>
                                                                            @empty
                                                                        <tr>
                                                                                <td colspan="7" class="text-center text-muted py-4">
                                                                                    Không có sản phẩm nào
                                                                                </td>
                                                                        </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                
                                                                <!-- Phần ghi chú và tổng kết -->
                                                                <div class="import-summary-section">
                                                                    <div class="import-notes-area">
                                                                        <label class="import-notes-label">Ghi chú:</label>
                                                                        <textarea class="import-notes-textarea" placeholder="Ghi chú..." readonly>{{ $import->note ?? '' }}</textarea>
                                                                    </div>
                                                                    
                                                                    <div class="import-summary-box">
                                                                        @php
                                                                            $totalItems = $import->items->count();
                                                                            $totalQuantity = $import->items->sum('quantity');
                                                                            $totalAmount = $import->items->sum('total_price');
                                                                            $totalDiscount = $import->items->sum('discount');
                                                                            $grandTotal = $totalAmount;
                                                                            $paidAmount = $import->total_amount ?? $grandTotal;
                                                                        @endphp
                                                                        
                                                                        <div class="import-summary-row">
                                                                            <span class="import-summary-label">Số lượng mặt hàng:</span>
                                                                            <span class="import-summary-value">{{ $totalItems }}</span>
                                                                        </div>
                                                                        <div class="import-summary-row">
                                                                            <span class="import-summary-label">Tổng tiền hàng ({{ $totalQuantity }}):</span>
                                                                            <span class="import-summary-value">{{ number_format($totalAmount, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        <div class="import-summary-row">
                                                                            <span class="import-summary-label">
                                                                                Giảm giá
                                                                                <i class="fas fa-info-circle import-info-icon"></i>
                                                                            </span>
                                                                            <span class="import-summary-value">{{ number_format($totalDiscount, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        <div class="import-summary-divider"></div>
                                                                        <div class="import-summary-row import-summary-total">
                                                                            <span class="import-summary-label">Tổng cộng:</span>
                                                                            <span class="import-summary-value">{{ number_format($grandTotal, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        <div class="import-summary-divider"></div>
                                                                        <div class="import-summary-row import-summary-paid">
                                                                            <span class="import-summary-label">Tiền đã trả NCC:</span>
                                                                            <span class="import-summary-value">{{ number_format($paidAmount, 0, ',', '.') }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Footer với các nút hành động -->
                                                                <div class="import-action-footer">
                                                                    <div class="import-action-left">
                                                                        <button class="import-action-btn import-btn-cancel">
                                                                            <i class="fas fa-trash"></i> Hủy
                                                                        </button>
                                                                        <button class="import-action-btn import-btn-copy">
                                                                            <i class="fas fa-copy"></i> Sao chép
                                                                        </button>
                                                                        <button class="import-action-btn import-btn-export">
                                                                            <i class="fas fa-download"></i> Xuất file
                                                                        </button>
                                                                    </div>
                                                                    
                                                                    <div class="import-action-right">
                                                                        
                                                                        <button class="import-action-btn import-btn-secondary">
                                                                            <i class="fas fa-save"></i> Lưu
                                                                        </button>
                                                                        <button class="import-action-btn import-btn-secondary">
                                                                            <i class="fas fa-undo"></i> Trả hàng nhập
                                                                        </button>
                                                                        <button class="import-action-btn import-btn-secondary">
                                                                            <i class="fas fa-barcode"></i> In thông tin
                                                                        </button>
                                                                        <button class="import-action-btn import-btn-more">
                                                                            <i class="fas fa-ellipsis-h"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Tab Content: Lịch sử nhập hàng -->
                                                        <div id="tab-history-{{ $import->id }}" class="tab-content" style="display: none;">
                                                             <div class="p-6">
                                                                 <table class="import-history-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Mã phiếu</th>
                                                                        <th class="col-time">Thời gian</th>
                                                                        <th class="col-creator">Người tạo</th>
                                                                        <th class="col-method">Phương thức</th>
                                                                        <th class="col-status">Trạng thái</th>
                                                                        <th class="col-amount">Tiền chi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <a href="#" class="payment-link">{{ $import->import_code }}001</a>
                                                                        </td>
                                                                        <td class="col-time">{{ \Carbon\Carbon::parse($import->import_date ?? $import->created_at)->format('d/m/Y H:i') }}</td>
                                                                        <td class="col-creator">admin</td>
                                                                        <td>Tiền mặt</td>
                                                                        <td class="col-status">
                                                                            <span class="badge-payment-paid">Đã nhập hàng</span>
                                                                        </td>
                                                                        <td class="col-amount">{{ number_format($import->total_amount ?? 0, 0, ',', '.') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                                </table>
                                                             </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                Không có phiếu nhập hàng nào
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
                                    @if($imports instanceof \Illuminate\Pagination\LengthAwarePaginator && $imports->total() > 0)
                                        Tổng cộng: <strong>{{ $imports->total() }}</strong> phiếu nhập
                                        Hiển thị : <strong>{{ $imports->firstItem() }}</strong> - <strong>{{ $imports->lastItem() }}</strong>
                                    @elseif(is_countable($imports) && count($imports) > 0)
                                        Tổng cộng: <strong>{{ count($imports) }}</strong> phiếu nhập
                                        Hiển thị : <strong>1</strong> - <strong>{{ count($imports) }}</strong>
                                    @else
                                        Chưa có phiếu nhập hàng nào
                                    @endif
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
<script src="{{ asset('js/products/supplier/import-management.js') }}"></script>
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

// Toggle import detail row
function toggleImportDetail(importId, rowElement) {
    const detailRow = document.getElementById('detail-row-' + importId);
    
    if (detailRow.style.display === 'none' || detailRow.style.display === '') {
        // Hide all other detail rows first
        document.querySelectorAll('.detail-row').forEach(row => {
            row.style.display = 'none';
        });
        
        // Show this detail row
        detailRow.style.display = 'table-row';
        
        // Remove selected-row class from all rows
        document.querySelectorAll('.import-row').forEach(row => {
            row.classList.remove('selected-row');
        });
        
        // Add selected-row class to current row
        rowElement.classList.add('selected-row');
    } else {
        // Hide this detail row
        detailRow.style.display = 'none';
        rowElement.classList.remove('selected-row');
    }
}

// Switch between tabs - sử dụng pattern từ Danhsachhanghoa/index.blade.php
function switchTab(importId, tabType, btnEl) {
    const keys = ['info', 'history'];

    keys.forEach(function(key){
        var panel = document.getElementById('tab-' + key + '-' + importId);
        if (panel) {
            panel.style.display = (key === tabType) ? 'block' : 'none';
        }
    });

    if (btnEl) {
        var wrapper = btnEl.parentElement; // .title-detail
        if (wrapper) {
            var buttons = wrapper.querySelectorAll('button.tab');
            buttons.forEach(function(b){ b.classList.remove('active'); });
        }
        btnEl.classList.add('active');
    }
}

// Prevent row click when clicking on tab buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>
@endpush

@endsection 