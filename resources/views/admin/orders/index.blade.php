@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Control Bar -->
    <div class="header-control-bar mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Title Section -->
            <div class="title-section">
                <h4 class="header-title mb-0">Danh sách đơn hàng</h4>
                <p class="text-muted small mb-0">Quản lý tất cả đơn đặt hàng</p>
            </div>
            <!-- Controls Section -->
            <div class="controls-section d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="fas fa-file-export me-2"></i>
                    Xuất file
                </button>
                <button class="btn btn-primary d-flex align-items-center">
                    <i class="fas fa-plus me-2"></i>
                    Tạo đơn hàng mới
                </button>
                <button id="printSelectedBtn" class="btn btn-success d-flex align-items-center">
                    <i class="fas fa-print me-2"></i>
                    In hóa đơn đã chọn
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section mb-4 bg-white p-3 rounded shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="row g-3 align-items-end">
                <!-- Order Number Filter -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Mã đơn hàng</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                        <input type="text" name="order_code" class="form-control" placeholder="Tìm theo mã" value="{{ request('order_code') }}">
                    </div>
                </div>
            
            <!-- Date Range Filter -->
            <div class="col-md-3">
                <label class="form-label small fw-bold">Ngày đặt hàng</label>
                <div class="input-group">
                    <input type="date" name="from_date" class="form-control" placeholder="Từ ngày" value="{{ request('from_date') }}">
                    <span class="input-group-text">-</span>
                    <input type="date" name="to_date" class="form-control" placeholder="Đến ngày" value="{{ request('to_date') }}">
                </div>
            </div>
            
            <!-- Status Filter -->
            <div class="col-md-2">
                <label class="form-label small fw-bold">Trạng thái</label>
                <select class="form-select">
                    <option selected value="">Tất cả</option>
                    <option value="pending">Đang chờ xử lý</option>
                    <option value="processing">Đang xử lý</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="cancelled">Đã hủy</option>
                </select>
            </div>
            
            <!-- Payment Method Filter -->
            <div class="col-md-2">
                <label class="form-label small fw-bold">Phương thức thanh toán</label>
                <select class="form-select">
                    <option selected value="">Tất cả</option>
                    <option value="cash">Tiền mặt</option>
                    <option value="transfer">Chuyển khoản Vnpay</option>
                </select>
            </div>          
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="30">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th scope="col">Mã đơn hàng</th>
                            <th scope="col">Tên khách hàng</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Phương thức thanh toán</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Ngày tạo</th>
                            <th scope="col" width="100">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr data-order-id="{{ $order->id }}">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input order-select" type="checkbox" value="{{ $order->id }}">
                                </div>
                            </td>
                            <td><span class="fw-medium">{{ $order->order_code }}</span></td>
                            <td>{{ $order->customer_name ?? 'N/A' }}</td>
                            <td>
                                @php($status = strtolower($order->order_status ?? ''))
                                @if($status === 'pending' || $status === 'new')
                                    <span class="badge bg-warning text-dark">Đang chờ xử lý</span>
                                @elseif($status === 'processing')
                                    <span class="badge bg-primary">Đang xử lý</span>
                                @elseif($status === 'completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                @elseif($status === 'cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                @else
                                    <span class="badge bg-secondary">Khác</span>
                                @endif
                            </td>
                            <td>{{ $order->payment_method }}</td>
                            <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Thao tác
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item view-order-detail" href="#" data-id="{{ $order->id }}"><i class="fas fa-eye me-2"></i>Xem chi tiết</a></li>
                                        <li><a class="dropdown-item edit-order" href="#" data-id="{{ $order->id }}"><i class="fas fa-pencil-alt me-2"></i>Chỉnh sửa</a></li>
                                        <li><a class="dropdown-item text-danger" href="{{ route('admin.orders.destroy', $order->id) }}"><i class="fas fa-trash-alt me-2"></i>Xóa</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach                                                                                                                                                     
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted small">Hiển thị 1-5 của 100 đơn hàng</span>
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@include('admin.orders.modals.details_modal')
@include('admin.orders.modals.edit_modals')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Select all checkbox (giữ tại index)
        $('#selectAll').change(function() {
            $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));
        });

        // In nhiều hóa đơn đã chọn
        $('#printSelectedBtn').on('click', function(e) {
            e.preventDefault();
            const selectedIds = $('tbody input.order-select:checked').map(function(){ return $(this).val(); }).get();
            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một đơn hàng để in.');
                return;
            }
            // Mở từng hóa đơn ở tab mới để trình duyệt xử lý in/ tải PDF
            selectedIds.forEach(function(id, idx) {
                const url = '{{ route("admin.orders.invoice", ["order" => ":id"]) }}'.replace(':id', id);
                // Thêm độ trễ nhỏ để tránh chặn pop-up trên một số trình duyệt
                setTimeout(function(){ window.open(url, '_blank'); }, idx * 250);
            });
        });

        // Auto submit filter form khi thay đổi input ngày hoặc mã đơn hàng
        const filterForm = $('.filter-section form');
        filterForm.find('input[name="from_date"], input[name="to_date"], input[name="order_code"]').on('change', function() {
            filterForm.submit();
        });
    });
</script>
@endpush
@endsection