@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div class="display-6 text-success mb-2">
                    <i class="fas fa-circle-check"></i>
                </div>
                <h3 class="fw-bold mb-2">Đặt hàng thành công!</h3>
                <p class="text-muted">Cảm ơn bạn đã mua sắm tại Sức Khỏe 24h.</p>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Mã đơn hàng</div>
                            <div class="fw-semibold">#{{ $order->id }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Ngày đặt</div>
                            <div class="fw-semibold">{{ optional($order->created_at)->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-muted">Trạng thái</div>
                            <span class="badge bg-success">Thành công</span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="small text-muted">Tổng tiền</div>
                            <div class="fs-5 text-danger fw-bold">{{ number_format($order->total_amount ?? $order->total ?? 0) }}đ</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Sản phẩm</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center" style="width:120px">SL</th>
                                    <th class="text-end" style="width:160px">Đơn giá</th>
                                    <th class="text-end" style="width:180px">Thành tiền</th>
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
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td class="text-center">{{ $qty }}</td>
                                        <td class="text-end">{{ number_format($price) }}đ</td>
                                        <td class="text-end">{{ number_format($lineTotal) }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-receipt me-2"></i>Xem đơn khác
                </a>
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="fas fa-house me-2"></i>Về trang chủ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


