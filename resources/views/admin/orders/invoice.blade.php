<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #{{ $order->order_code }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #333; padding: 6px; text-align: left; }
        .table th { background: #eee; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>HÓA ĐƠN BÁN HÀNG</h2>
        <p>Mã đơn hàng: <strong>{{ $order->order_code }}</strong></p>
        <p>Ngày: {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <p><strong>Khách hàng:</strong> {{ $order->customer_name }}</p>
    <p><strong>SĐT:</strong> {{ $order->customer_phone }}</p>
    <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product_name ?? $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="total">Tổng cộng</td>
                <td class="total">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
            </tr>
        </tfoot>
    </table>

    <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>
    <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
</body>
</html>