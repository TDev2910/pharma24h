<div class="card shadow-sm" style="margin-left: 15px;">
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead class="table-light">
      <tr>
        <th style="width:60px;">STT</th>
        <th style="min-width:140px;">Mã hàng</th>
        <th style="min-width:240px;">Tên hàng</th>
        <th style="width:90px;">ĐVT</th>
        <th style="width:120px;">Số lượng</th>
        <th style="width:140px;">Đơn giá</th>
        <th class="text-end" style="width:130px;">Thành tiền</th>
      </tr>
      </thead>
      <tbody id="importItemsBody">
        <tr>
          <!-- Nếu bạn có 9 cột như hiện tại, giữ 9; nếu đã thêm cột Giảm giá thì đổi thành 10 -->
          <td colspan="9" class="empty-state-cell">
            <div class="empty-wrapper">
              <div class="fw-semibold mb-2" style="font-size:20px;color:#2b2f33;">
                Thêm sản phẩm từ file excel
              </div>
              <div class="text-muted mb-3" style="font-size:14px;">
                (Tải về file mẫu:
                <a href="#" class="ms-1" style="text-decoration:underline;">Excel file</a>)
              </div>
              <button type="button" class="btn btn-primary btn-lg" style="border-radius: 5px;">
                Chọn file dữ liệu
              </button>
            </div>
          </td>        
        </tr>
      </tbody>
    </table>
  </div>
</div>

<style>
  .empty-state-cell {
  height: 100%;
  padding: 0;
}
.empty-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center; /* căn giữa dọc */
  align-items: center;     /* căn giữa ngang */
  min-height: 615px;       /* ép chiều cao khung */
}

</style>
