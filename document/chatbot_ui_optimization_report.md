# BÁO CÁO CẬP NHẬT VÀ TỐI ƯU HÓA GIAO DIỆN CHATBOT
**Ngày cập nhật:** 21/03/2026

## 1. Bối cảnh & Vấn đề ban đầu (Trạng thái "Rời rạc")
Trước khi cập nhật, giao diện các nút bấm nổi (Floating Buttons) gặp phải các vấn đề nghiêm trọng sau:

*   **Vị trí độc lập:** Nút Robot AI (`ChatbotFloatingButton`) và nút FAB xanh (`ChatWidget`) được định vị bằng các thuộc tính `position: fixed` hoàn toàn tách biệt. Điều này khiến chúng dễ dàng bị chồng đè lên nhau hoặc lệch lạc không thẳng hàng trên các kích thước màn hình khác nhau (PC và Mobile).
*   **Trùng lặp & Dư thừa:** Trong `Footer.vue` có tới 3 loại Chat (AI Chat, Human Chat cũ, Human Chat mới), gây ra sự chồng chéo về chức năng và làm nặng giao diện.
*   **Vấn đề Mobile:** Các thông số `margin` âm cứng (ví dụ: `-250px`) được dùng trực tiếp trong code Vue, khiến việc tinh chỉnh trên Desktop trở nên cực kỳ khó khăn (vì sửa cho Mobi thì hỏng cho PC).
*   **Lỗi Event Listener:** Khi cố gắng dọn dẹp các component dư thừa, `ChatWidget` đã bị xóa hoàn toàn. Điều này làm mất trình lắng nghe (listener) cho sự kiện `'open-human-chat'`, dẫn đến việc nhấn nút "Nhân viên" không có phản ứng gì.

---

## 2. Giải pháp Cập nhật & Tối ưu hóa

### 2.1. Gom nhóm (Unification)
Chuyển đổi từ mô hình "mỗi nút một nơi" sang mô hình "Khối thống nhất" (Grouped). Toàn bộ các nút hiện đã được đưa vào chung một thẻ cha `.fab-wrapper` trong `Footer.vue`. 

**Lợi ích:** Khi bạn thay đổi vị trí của `.fab-wrapper`, toàn bộ bộ máy (Robot, FAB chính, Messenger, Zalo) sẽ di chuyển cùng nhau một cách đồng bộ.

### 2.2. Tái cấu trúc Layout (Flexbox)
Sử dụng CSS Flexbox để quản lý sự thẳng hàng. Chúng ta đã tạo ra một cấu trúc hàng dọc (`column`) cho Robot và nút FAB:
*   **Robot** nằm trên.
*   **FAB Main** (nút xanh) nằm dưới.
*   **Khoảng cách (`gap`)** cố định là `10px`, đảm bảo chúng không bao giờ đè lên nhau.

### 2.3. Sửa lỗi "Nhân viên" (Human Chat)
Khôi phục component `ChatWidget` vào `Footer.vue` nhưng với tham số `:show-trigger="false"`.
*   **Cơ chế:** Component này giờ đây đóng vai trò là "người nghe ngầm". Nó không hiển thị thêm bất kỳ nút nào trên màn hình (vì ta đã có nút trong FAB), nhưng nó vẫn duy trì listener để mở Popup Chat khi người dùng nhấn vào nút "Nhân viên".

---

## 3. Bảng so sánh Trước & Sau

| Đặc điểm | Trước (Rời rạc) | Sau (Gom nhóm) |
| :--- | :--- | :--- |
| **Quản lý vị trí** | Sửa `right/bottom` ở nhiều file khác nhau. | Sửa **1 nơi duy nhất** tại `.fab-wrapper` trong `Footer.vue`. |
| **Sự thẳng hàng** | Thường xuyên bị lệch, chồng đè lên nhau. | **Thẳng hàng ngang & dọc tuyệt đối** nhờ Flexbox. |
| **Responsive** | Phải viết Media Query riêng cho từng component. | Media Query áp dụng chung cho cả khối, đảm bảo tính thẩm mỹ. |
| **Nút "Nhân viên"** | Không hoạt động (mất Listener). | ✅ Đã hoạt động trở lại bằng cách chạy Component ngầm. |
| **Code Style** | Dùng style inline (`style="..."`). | ✅ Chuyển sang dùng Class CSS chuyên biệt, code sạch hơn. |

---

## 4. Hướng dẫn Tinh chỉnh & Duy trì

### Làm thế nào để di chuyển cả cụm nút lên/xuống/trái/phải?
Bạn mở file `f:\laragon\laragon\www\suckhoe24h\resources\js\Components\Global\Footer.vue` và tìm đến block CSS khoảng dòng 265:

```css
.fab-wrapper {
  position: fixed;
  bottom: 100px; /* Chỉnh độ cao so với đáy trang */
  right: 10px;   /* Chỉnh khoảng cách so với lề phải */
  ...
}
```

### Làm thế nào để chỉnh khoảng cách giữa Robot và Nút xanh?
Tìm class `.fab-bottom-row` trong `Footer.vue` và thay đổi giá trị `gap`:

```css
.fab-bottom-row {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px; /* Tăng/giảm số này để chỉnh khoảng cách */
}
```

---
**Báo cáo kết thúc: Hệ thống hiện đã hoạt động ổn định và chuyên nghiệp hơn.**
