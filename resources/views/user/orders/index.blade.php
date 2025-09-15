@extends('layouts.user')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Đơn hàng của tôi</h2>
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>

            <!-- Danh sách đơn hàng đã mua -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Đơn hàng đã đặt</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr class="title">
                                <th>Mã đơn hàng</th>
                                <th>Họ tên</th>
                                <th>Số điện thoại</th>
                                <th>Tổng tiền</th>
                                <th>Ngày mua</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết đơn hàng</th>
                            </tr>
                        </thead>
                        <tbody class="title">
                            @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->order_code ?? sprintf('%04d', $order->id) }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_phone }}</td>
                                    <td>{{ number_format($order->total_amount) }} đ</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($order->order_status == 'new')
                                            <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                        @elseif($order->order_status == 'processing')
                                            <span class="badge bg-info text-dark">Đang xử lý</span>
                                        @elseif($order->order_status == 'shipped')
                                            <span class="badge bg-primary">Đã giao</span>
                                        @elseif($order->order_status == 'delivered')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @elseif($order->order_status == 'cancelled')
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                        @endif
                                    </td>
                                    <td class="details-order">
                                        <a href="{{ route('user.orders.details', $order->id) }}" class="btn btn-info btn-sm">Xem chi tiết</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Bạn chưa có đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .title
    {
        text-align: center;
    }

    .details-order {
        text-align: center
    }
</style>
