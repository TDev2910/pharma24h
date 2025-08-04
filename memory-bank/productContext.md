# Product Context - Suckhoe24h

## Problem Statement
Hệ thống quản lý dược phẩm cần một giao diện hiệu quả để:
- Quản lý danh sách thuốc với thông tin chi tiết
- Thực hiện các thao tác CRUD một cách trực quan
- Hiển thị dữ liệu có cấu trúc và dễ đọc
- Tối ưu workflow cho người dùng admin

## User Experience Goals
1. **Intuitive Navigation**: Dễ dàng chuyển đổi giữa các chức năng
2. **Efficient Data Entry**: Form nhập liệu với validation và auto-complete
3. **Responsive Design**: Giao diện hoạt động tốt trên nhiều thiết bị
4. **Real-time Feedback**: Thông báo ngay lập tức về kết quả thao tác
5. **Consistent UI**: Thiết kế nhất quán trong toàn bộ hệ thống

## Key Features
### Medicine Management
- **List View**: Hiển thị danh sách thuốc với thông tin cơ bản
- **Search & Filter**: Tìm kiếm và lọc theo nhiều tiêu chí
- **Modal Forms**: Tạo và chỉnh sửa thuốc trong modal popup
- **Inline Creation**: Tạo mới manufacturer, drug route, position trực tiếp từ form

### Data Relationships
- **Manufacturer**: Nhà sản xuất thuốc
- **Drug Route**: Đường dùng thuốc
- **Position**: Vị trí lưu trữ
- **Category**: Phân loại sản phẩm

## User Workflows
1. **Adding New Medicine**:
   - Click "Thêm thuốc" → Modal opens
   - Fill form with medicine details
   - Create related entities inline if needed
   - Save → Success notification

2. **Editing Medicine**:
   - Click "Chỉnh sửa" → Modal opens with pre-filled data
   - Modify fields as needed
   - Save → Success notification

3. **Deleting Medicine**:
   - Click "Xóa" → Confirmation dialog
   - Confirm → Remove from database

## Design Principles
- **Compact Layout**: Tối ưu không gian hiển thị
- **Visual Hierarchy**: Thông tin quan trọng nổi bật
- **Action-Oriented**: Các nút thao tác dễ tiếp cận
- **Consistent Styling**: Màu sắc và spacing nhất quán 