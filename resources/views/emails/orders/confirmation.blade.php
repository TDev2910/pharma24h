<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f5f5f5; color: #333333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">

                    <tr>
                        <td style="background-color: #2563eb; padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">SỨC KHỎE 24H</h1>
                            <p style="color: #e0e7ff; margin: 10px 0 0 0; font-size: 14px;">Xác nhận đơn hàng thành công</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
                                Chào <strong>{{ $order->customer_name }}</strong>,<br>
                                Cảm ơn bạn đã đặt hàng. Dưới đây là thông tin chi tiết về đơn hàng của bạn:
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                                <tr>
                                    <td style="padding-bottom: 10px;">
                                        <span style="color: #6b7280; font-size: 14px;">Mã đơn hàng:</span><br>
                                        <strong style="color: #2563eb; font-size: 16px;">#{{ $order->order_code ?? $order->id }}</strong>
                                    </td>
                                    <td style="text-align: right; padding-bottom: 10px;">
                                        <span style="color: #6b7280; font-size: 14px;">Ngày đặt:</span><br>
                                        <strong style="color: #333; font-size: 14px;">{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 12px; text-align: left; color: #4b5563; font-size: 13px; text-transform: uppercase;">Sản phẩm</th>
                                        <th style="padding: 12px; text-align: center; color: #4b5563; font-size: 13px; text-transform: uppercase; width: 60px;">SL</th>
                                        <th style="padding: 12px; text-align: right; color: #4b5563; font-size: 13px; text-transform: uppercase; width: 120px;">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $subtotal = 0; @endphp
                                    @foreach ($order->items as $item)
                                    @php
                                        $itemTotal = $item->price * $item->quantity;
                                        $subtotal += $itemTotal;
                                    @endphp
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 12px; font-size: 14px; color: #111827;">
                                            {{ $item->product_name }}
                                            <div style="color: #6b7280; font-size: 12px; margin-top: 4px;">Đơn giá: {{ number_format($item->price) }}đ</div>
                                        </td>
                                        <td style="padding: 12px; text-align: center; font-size: 14px; color: #374151;">{{ $item->quantity }}</td>
                                        <td style="padding: 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">{{ number_format($itemTotal) }}đ</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="padding: 15px 12px 5px; text-align: right; color: #6b7280; font-size: 14px;">Tạm tính:</td>
                                        <td style="padding: 15px 12px 5px; text-align: right; color: #111827; font-size: 14px;">{{ number_format($subtotal) }}đ</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 5px 12px; text-align: right; color: #6b7280; font-size: 14px;">Phí vận chuyển:</td>
                                        <td style="padding: 5px 12px; text-align: right; color: #111827; font-size: 14px;">
                                            @if($order->shipping_fee > 0)
                                                {{ number_format($order->shipping_fee) }}đ
                                            @else
                                                <span style="color: #059669; font-weight: bold;">Miễn phí</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 15px 12px; text-align: right; font-weight: bold; color: #111827; font-size: 16px; border-top: 1px dashed #e5e7eb;">Tổng thanh toán:</td>
                                        <td style="padding: 15px 12px; text-align: right; font-weight: bold; color: #dc2626; font-size: 18px; border-top: 1px dashed #e5e7eb;">
                                            {{ number_format($order->total_amount) }}đ
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div style="margin-top: 30px; border-top: 1px solid #f3f4f6; padding-top: 20px; text-align: center;">
                                <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
                                    Đơn hàng sẽ sớm được giao đến bạn.
                                </p>
                                <a href="{{ url('/') }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;">Tiếp tục mua sắm</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="font-size: 12px; color: #9ca3af; margin: 0 0 8px 0;">
                                <strong>Sức Khỏe 24h</strong> - Chăm sóc sức khỏe toàn diện
                            </p>
                            <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                                Hotline: 1900 xxxx | Email: support@suckhoe24h.com
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 11px; color: #9ca3af; margin-top: 20px;">
                    Đây là email tự động, vui lòng không trả lời email này.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
