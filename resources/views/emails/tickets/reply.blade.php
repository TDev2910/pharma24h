<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { background-color: #B4DEBD; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; font-weight: bold; color: #004d40; }
        .content { padding: 20px; }
        .ticket-info { background: #f8f9fa; padding: 10px; border-left: 4px solid #17a2b8; margin-bottom: 20px; }
        .reply-box { background: #e8f5e9; padding: 15px; border-radius: 5px; border: 1px solid #c8e6c9; }
        .footer { font-size: 12px; text-align: center; color: #888; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            PHẢN HỒI HỖ TRỢ PHARMA24H
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $ticket->full_name }}</strong>,</p>
            <p>Chúng tôi đã nhận được yêu cầu của bạn và đây là phản hồi từ quản trị viên:</p>

            <div class="ticket-info">
                <strong>Mã yêu cầu:</strong> #{{ $ticket->ticket_id }}<br>
                <strong>Chủ đề:</strong> {{ $ticket->subject }}
            </div>

            <p><strong>Nội dung phản hồi:</strong></p>
            <div class="reply-box">
                {!! nl2br(e($replyContent)) !!}
            </div>

            <p style="margin-top: 20px;">Nếu bạn có thêm thắc mắc, vui lòng trả lời email này.</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Pharma24h. All rights reserved.
        </div>
    </div>
</body>
</html>
