@extends('layouts.user')

@section('content')
<div class="container my-4">
	<h2>Chi tiết đơn hàng</h2>
	<div class="card mb-4">
		<div class="card-body">
			<h5 class="card-title">Mã đơn hàng: <span class="text-primary">#{{ $order->order_code ?? sprintf('%04d', $order->id) }}</span></h5>
			<p><strong>Khách hàng:</strong> {{ $order->customer_name }}</p>
			<p><strong>Email:</strong> {{ $order->user->email ?? '' }}</p>
			<p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
			<p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}
				@if($order->province || $order->district || $order->ward)
					({{ $order->ward }}, {{ $order->district }}, {{ $order->province }})
				@endif
			</p>
			<p><strong>Phương thức giao hàng:</strong> {{ $order->delivery_method == 'pickup' ? 'Nhận tại nhà thuốc' : 'Giao hàng tận nơi' }}</p>
			<p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>
			<p><strong>Trạng thái thanh toán:</strong> {{ $order->payment_status }}</p>
			<p><strong>Trạng thái đơn hàng:</strong> {{ $order->order_status }}</p>
			<p><strong>Ghi chú:</strong> {{ $order->note }}</p>
			<p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
		</div>
	</div>

	<h5>Danh sách sản phẩm</h5>
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>#</th>
					<th>Tên sản phẩm</th>
					<th>Số lượng</th>
					<th>Đơn giá</th>
					<th>Thành tiền</th>
				</tr>
			</thead>
			<tbody>
				@foreach($order->items as $index => $item)
				<tr>
					<td>{{ $index + 1 }}</td>
					<td>{{ $item->product_name ?? $item->name }}</td>
					<td>{{ $item->quantity }}</td>
					<td>{{ number_format($item->price) }} đ</td>
					<td>{{ number_format($item->price * $item->quantity) }} đ</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="text-end">
		<h5>Tổng tiền: <span class="text-danger">{{ number_format($order->total_amount) }} đ</span></h5>
	</div>
</div>
@endsection
