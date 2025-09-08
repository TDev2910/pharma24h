@extends('layouts.app')

@section('title', 'Giỏ hàng - PCT Pharma')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Giỏ hàng của bạn</h1>
    
    @if(count($cartData['items']) > 0)
        <div class="cart-table">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead> 
                        <tr>
                            <th style="width: 50%">Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartData['items'] as $item)
                        <tr class="cart-item" data-id="{{ $item->id }}">
                            <td>
                                <div class="d-flex">
                                    <div class="cart-item-image me-3" style="width: 80px;">
                                        <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('images/products/default.jpg') }}" 
                                             alt="{{ $item->name }}" class="img-fluid">
                                    </div>
                                    <div>
                                        <h5>{{ $item->name }}</h5>
                                        <p class="text-muted small">{{ $item->item_type == 'medicine' ? 'Thuốc' : 'Hàng hóa' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                            <td class="align-middle">
                                <div class="cart-item-quantity d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary btn-qty-decrease">-</button>
                                    <input type="number" value="{{ $item->quantity }}" min="1" max="99" 
                                           class="form-control form-control-sm mx-2 text-center item-quantity" 
                                           style="width: 60px;">
                                    <button class="btn btn-sm btn-outline-secondary btn-qty-increase">+</button>
                                </div>
                            </td>
                            <td class="align-middle">{{ number_format($item->quantity * $item->price, 0, ',', '.') }} VNĐ</td>
                            <td class="align-middle text-center">
                                <button class="btn btn-sm btn-danger cart-item-remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                            <td colspan="2"><strong class="text-danger fs-5">{{ number_format($cartData['total'], 0, ',', '.') }} VNĐ</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <div class="cart-actions mt-4 d-flex justify-content-between">
            <a href="/" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Tiếp tục mua sắm
            </a>
            <a href="/checkout" class="btn btn-primary">
                Tiến hành thanh toán
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-3x text-muted"></i>
            </div>
            <h3>Giỏ hàng trống</h3>
            <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
            <a href="/" class="btn btn-primary mt-3">
                <i class="fas fa-shopping-bag me-2"></i>
                Tiếp tục mua sắm
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
@endpush
