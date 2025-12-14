@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="success-page-wrapper bg-light min-vh-100 d-flex align-items-center" style="padding-top: 80px; padding-bottom: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <!-- Success Icon & Message -->
                <div class="text-center mb-4">
                    <div class="success-icon-wrapper mb-3">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 1.75rem;">Đặt hàng thành công!</h2>
                    <p class="text-muted mb-0">Cảm ơn bạn đã mua sắm tại Sức Khỏe 24h.</p>
                </div>

                <!-- Thông tin đơn hàng -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary-subtle text-primary me-2">
                                <i class="pi pi-receipt"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Thông tin đơn hàng</h5>
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Mã đơn hàng</div>
                                <div class="info-value">#{{ $order->order_code ?? sprintf('%04d', $order->id) }}</div>
                            </div>
                            <div class="info-item text-md-end">
                                <div class="info-label">Ngày đặt</div>
                                <div class="info-value">{{ optional($order->created_at)->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="info-grid mt-3">
                            <div class="info-item">
                                <div class="info-label">Trạng thái</div>
                                <span class="badge bg-success-subtle text-success fw-semibold px-3 py-2">Thành công</span>
                            </div>
                            <div class="info-item text-md-end">
                                <div class="info-label">Tổng tiền</div>
                                <div class="info-value text-danger fs-5 fw-bold">{{ number_format($order->total_amount ?? $order->total ?? 0) }}đ</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sản phẩm -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary-subtle text-primary me-2">
                                <i class="pi pi-shopping-cart"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Sản phẩm</h5>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-borderless modern-table mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="fw-semibold text-muted small pb-3">SẢN PHẨM</th>
                                        <th class="fw-semibold text-muted small pb-3 text-center" style="width:80px">SL</th>
                                        <th class="fw-semibold text-muted small pb-3 text-end" style="width:140px">ĐƠN GIÁ</th>
                                        <th class="fw-semibold text-muted small pb-3 text-end" style="width:140px">THÀNH TIỀN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        @php
                                            $name = $item->product_name ?? $item->name ?? 'Sản phẩm';
                                            $qty = $item->quantity ?? 1;
                                            $price = $item->price ?? $item->unit_price ?? 0;
                                            $lineTotal = $item->total_price ?? ($qty * $price);
                                        @endphp
                                        <tr class="border-bottom">
                                            <td class="py-3">{{ $name }}</td>
                                            <td class="py-3 text-center">{{ $qty }}</td>
                                            <td class="py-3 text-end">{{ number_format($price) }}đ</td>
                                            <td class="py-3 text-end fw-semibold">{{ number_format($lineTotal) }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-dark btn-lg px-4 rounded-3">
                        <i class="pi pi-list me-2"></i>Xem đơn khác
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-4 rounded-3">
                        <i class="pi pi-home me-2"></i>Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.success-page-wrapper {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.success-icon-wrapper {
    display: inline-block;
}

.success-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
    animation: scaleIn 0.5s ease-out;
}

.success-icon i {
    color: white;
    font-size: 2.5rem;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.15) !important;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
}

.modern-table thead th {
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none !important;
}

.modern-table tbody tr:last-child {
    border-bottom: none !important;
}

.modern-table tbody td {
    color: #495057;
    font-size: 0.95rem;
}

.card {
    transition: transform 0.2s ease;
}

.btn {
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-outline-dark {
    border-width: 2px;
}

.btn-outline-dark:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
    background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .info-item.text-md-end {
        text-align: left !important;
    }
    
    .success-icon {
        width: 70px;
        height: 70px;
    }
    
    .success-icon i {
        font-size: 2rem;
    }
}
</style>
@endpush


