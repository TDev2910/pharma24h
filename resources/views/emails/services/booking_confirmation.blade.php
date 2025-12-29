<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đặt lịch</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;">
        <h2 style="color: #2c3e50; text-align: center;">Cảm ơn bạn đã đặt lịch tại Pharma24h!</h2>

        <p>Xin chào <strong>{{ $booking->customer_name }}</strong>,</p>

        <p>Chúng tôi đã nhận được yêu cầu đặt lịch của bạn. Dưới đây là thông tin chi tiết:</p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Dịch vụ:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $booking->service->name ?? 'Dịch vụ đang cập nhật' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Ngày hẹn:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Giờ hẹn:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Giá dự kiến:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ number_format($booking->price) }} VNĐ</td>
            </tr>
        </table>

        <p>Vui lòng đến đúng giờ để được phục vụ tốt nhất.</p>

        <p>Trân trọng,<br>Đội ngũ Pharma24h</p>
    </div>
</body>
</html>
