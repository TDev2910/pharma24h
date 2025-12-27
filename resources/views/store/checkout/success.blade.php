@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

                    <div class="bg-success text-white text-center py-4">
                        <div class="mb-3">
                            <i class="pi pi-check-circle" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="fw-bold m-0">Đặt hàng thành công!</h2>
                        <p class="mb-0 opacity-75 mt-1">Cảm ơn bạn đã mua sắm tại Sức Khỏe 24h</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="row mb-4 border-bottom pb-4 g-3">
                            <div class="col-md-6">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Mã đơn hàng</span>
                                <span class="fs-5 fw-bold text-primary">#{{ $order->order_code ?? $order->id }}</span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Thời gian đặt</span>
                                <span class="fw-medium text-dark">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="col-12 mt-3">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Phương thức thanh toán</span>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Thanh toán online (VNPAY)' }}
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead class="border-bottom bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 text-secondary small text-uppercase" style="width: 50%">Sản phẩm</th>
                                        <th class="py-3 text-center text-secondary small text-uppercase" style="width: 20%">SL</th>
                                        <th class="py-3 pe-3 text-end text-secondary small text-uppercase" style="width: 30%">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Tính lại subtotal từ items để hiển thị chính xác
                                        $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
                                    @endphp

                                    @foreach($order->items as $item)
                                    <tr class="border-bottom border-light">
                                        <td class="py-3 ps-3">
                                            <div class="fw-medium text-dark">{{ $item->product_name ?? $item->name }}</div>
                                            <div class="text-muted small">Đơn giá: {{ number_format($item->price) }}đ</div>
                                        </td>
                                        <td class="py-3 text-center align-middle">{{ $item->quantity }}</td>
                                        <td class="py-3 pe-3 text-end align-middle fw-medium">
                                            {{ number_format($item->price * $item->quantity) }}đ
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end pt-4 pb-2 text-muted">Tạm tính:</td>
                                        <td class="text-end pt-4 pb-2 pe-3 fw-medium">{{ number_format($subtotal) }}đ</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end py-2 text-muted">Phí vận chuyển:</td>
                                        <td class="text-end py-2 pe-3 fw-medium">
                                            @if($order->shipping_fee > 0)
                                                {{ number_format($order->shipping_fee) }}đ
                                            @else
                                                <span class="text-success">Miễn phí</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end pt-3 text-dark fw-bold fs-5 border-top">Tổng thanh toán:</td>
                                        <td class="text-end pt-3 pe-3 text-danger fw-bold fs-5 border-top">
                                            {{ number_format($order->total_amount) }}đ
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-5">
                            <a href="{{ route('home') }}" class="btn btn-primary py-3 px-5 rounded-pill fw-bold">
                                <i class="pi pi-home me-2"></i> Về trang chủ
                            </a>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary py-3 px-5 rounded-pill fw-bold">
                                <i class="pi pi-shopping-bag me-2"></i> Mua thêm
                            </a>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted small mb-0">Mọi thắc mắc vui lòng liên hệ hotline: <strong class="text-dark">1900 xxxx</strong></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* CSS bổ trợ nhỏ để tinh chỉnh, còn lại dùng Bootstrap */
    .bg-light { background-color: #f8f9fa !important; }
    .card { box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important; }
    .table-responsive { scrollbar-width: thin; }

    /* Hiệu ứng nút */
    .btn { transition: all 0.2s; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
</style>
@endpush
