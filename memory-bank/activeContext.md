# Active Context - Cart System Development

## Current Focus
Đang phát triển chức năng **giỏ hàng (Cart System)** cho ứng dụng Suckhoe24h. Đây là tính năng quan trọng cho phép người dùng mua sắm các sản phẩm y tế.

## Recent Changes
- Header component đã có icon giỏ hàng với badge hiển thị số lượng (dòng 75-78)
- Badge hiện tại hiển thị số 0 cố định
- Cần tích hợp chức năng thực tế để cập nhật số lượng

## Next Steps
1. **Thiết kế Database Schema** cho Cart và CartItem
2. **Tạo Models** Cart và CartItem với relationships
3. **Tạo CartController** với các API endpoints
4. **Cập nhật Frontend** để xử lý AJAX requests
5. **Tích hợp với Header** để cập nhật badge real-time

## Active Decisions
- Sử dụng **Session-based cart** cho guest users
- Sử dụng **Database cart** cho authenticated users
- **Hybrid approach**: Kết hợp cả hai để có trải nghiệm tốt nhất
- **Real-time updates** sử dụng AJAX thay vì page reload

## Technical Considerations
- **Performance**: Cart operations phải nhanh và responsive
- **Data Integrity**: Đảm bảo số lượng sản phẩm không âm
- **User Experience**: Smooth transitions và feedback
- **Scalability**: Thiết kế để dễ mở rộng trong tương lai

## Current Cart Requirements
- Thêm sản phẩm vào giỏ hàng
- Cập nhật số lượng sản phẩm
- Xóa sản phẩm khỏi giỏ hàng
- Hiển thị tổng số lượng trong header badge
- Lưu trữ giỏ hàng cho user đã đăng nhập
- Giỏ hàng tạm thời cho guest users
