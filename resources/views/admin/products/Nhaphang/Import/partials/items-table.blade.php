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
  justify-content: center;
  align-items: center;    
  min-height: 615px;       
}
</style>

<script>
      document.addEventListener('DOMContentLoaded', function() {
          console.log('DOM loaded, initializing Excel import...');
          
          const importItemsBody = document.getElementById('importItemsBody');
          let isImporting = false; // guard to avoid double runs
          
          // Hiển thị empty state ban đầu
          showEmptyState();

          function showEmptyState() {
              importItemsBody.innerHTML = `
                  <tr>
                      <td colspan="7" class="empty-state-cell">
                          <div class="empty-wrapper">
                              <div class="fw-semibold mb-2" style="font-size:20px;color:#2b2f33;">
                                  Thêm sản phẩm từ file excel
                              </div>
                              <button type="button" id="selectExcelBtn" class="btn btn-primary btn-lg" style="border-radius: 5px;">
                                  Chọn file dữ liệu
                              </button>
                          </div>
                      </td>
                  </tr>
              `;
              
              // Gắn event listener cho nút mới
              const selectExcelBtn = document.getElementById('selectExcelBtn');
              if (selectExcelBtn) {
                  selectExcelBtn.addEventListener('click', function() {
                      console.log('Button clicked, opening file dialog...');
                      fileInput.click();
                  });
              }
          }

          // Tạo input file ẩn (một lần)
          const fileInput = document.createElement('input');
          fileInput.type = 'file';
          fileInput.accept = '.xlsx,.xls,.csv';
          fileInput.style.display = 'none';
          document.body.appendChild(fileInput);

          // Xử lý khi chọn file
          fileInput.addEventListener('change', function(e) {
              const file = e.target.files[0];
              console.log('File selected:', file ? file.name : 'No file');
              if (file && !isImporting) {
                  isImporting = true;
                  uploadExcelFile(file);
              }
          });
  
      function uploadExcelFile(file) {
          console.log('Starting upload for file:', file.name);
          
          const formData = new FormData();
          formData.append('excel_file', file);
          formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

          // Hiển thị loading
          importItemsBody.innerHTML = `
              <tr>
                  <td colspan="7" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                          <span class="visually-hidden">Đang xử lý...</span>
                      </div>
                      <div class="mt-2">Đang xử lý file Excel...</div>
                  </td>
              </tr>
          `;

          console.log('Sending request to:', '{{ route("admin.process-excel") }}');
          
          fetch('{{ route("admin.process-excel") }}', {
              method: 'POST',
              body: formData,
              headers: {
                  'X-Requested-With': 'XMLHttpRequest'
              }
          })
          .then(response => {
              console.log('Response received:', response.status);
              return response.json();
          })
          .then(data => {
              console.log('Data received:', data);
              if (data.success) {
                  displayImportedItems(data.items);
                  if (data.errors.length > 0) {
                      showErrors(data.errors);
                  }
              } else {
                  showError(data.message);
              }
          })
          .catch(error => {
              console.error('Error:', error);
              showError('Có lỗi xảy ra khi xử lý file');
          })
          .finally(() => {
              // reset guard and input so user can choose again manually
              isImporting = false;
              fileInput.value = '';
          });
      }
  
      function displayImportedItems(items) {
          if (items.length === 0) {
              showEmptyState();
              return;
          }
  
          let html = '';
          items.forEach((item, index) => {
              html += `
                  <tr data-product-type="${item.product_type}" data-product-id="${item.product_id}">
                      <td>${index + 1}</td>
                      <td>${item.ma_hang}</td>
                      <td>${item.ten_hang}</td>
                      <td>${item.don_vi_tinh}</td>
                      <td>
                          <input type="number" class="form-control form-control-sm quantity-input" 
                                 value="${item.so_luong}" min="1" data-index="${index}">
                      </td>
                      <td>
                          <input type="number" class="form-control form-control-sm price-input" 
                                 value="${item.don_gia}" min="0" step="0.01" data-index="${index}">
                      </td>
                      <td class="text-end">
                          <span class="total-price">${item.thanh_tien.toLocaleString('vi-VN')}</span>
                      </td>
                  </tr>
              `;
          });
  
          importItemsBody.innerHTML = html;
  
          // Gắn event listeners cho input
          document.querySelectorAll('.quantity-input, .price-input').forEach(input => {
              input.addEventListener('input', function() {
                  updateRowTotal(this);
                  updateSummary();
              });
          });
      }
  
      function updateRowTotal(input) {
          const row = input.closest('tr');
          const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
          const price = parseFloat(row.querySelector('.price-input').value) || 0;
          const total = quantity * price;
          
          row.querySelector('.total-price').textContent = total.toLocaleString('vi-VN');
      }
  
      function updateSummary() {
          // Cập nhật tổng tiền trong summary panel
          const totalInput = document.querySelector('input[name="subtotal_raw"]');
          if (totalInput) {
              let total = 0;
              document.querySelectorAll('.total-price').forEach(span => {
                  total += parseFloat(span.textContent.replace(/[^\d]/g, '')) || 0;
              });
              totalInput.value = total;
              totalInput.dispatchEvent(new Event('input'));
          }
      }
  
      function showErrors(errors) {
          const errorHtml = errors.map(error => `<div class="text-danger">• ${error}</div>`).join('');
          importItemsBody.innerHTML += `
              <tr>
                  <td colspan="7" class="text-danger">
                      <strong>Lỗi:</strong>
                      ${errorHtml}
                  </td>
              </tr>
          `;
      }

      function showError(message) {
          importItemsBody.innerHTML = `
              <tr>
                  <td colspan="7" class="text-center py-5 text-danger">
                      <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                      <div>${message}</div>
                      <button type="button" class="btn btn-outline-primary mt-3" onclick="location.reload()">
                          Thử lại
                      </button>
                  </td>
              </tr>
          `;
      }
  
      function showEmptyState() {
          importItemsBody.innerHTML = `
              <tr>
                  <td colspan="7" class="empty-state-cell">
                      <div class="empty-wrapper">
                          <div class="fw-semibold mb-2" style="font-size:20px;color:#2b2f33;">
                              Thêm sản phẩm từ file excel
                          </div>
                          <div class="text-muted mb-3" style="font-size:14px;">
                              (Tải về file mẫu:
                              <a href="#" class="ms-1" style="text-decoration:underline;">Excel file</a>)
                          </div>
                          <button type="button" id="selectExcelBtn" class="btn btn-primary btn-lg" style="border-radius: 5px;">
                              Chọn file dữ liệu
                          </button>
                      </div>
                  </td>
              </tr>
          `;
          
          // Gắn lại event listener cho nút mới
          document.getElementById('selectExcelBtn')?.addEventListener('click', function() {
              fileInput.click();
          });
      }
  });
</script>
