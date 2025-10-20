{{-- <!-- Modal tạo dịch vụ -->
<div class="modal fade create-modal" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header">
          <h5 class="modal-title" id="createServiceModalLabel">
            <i style="margin-right:10px"></i>Tạo dịch vụ 
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body p-4">
              <!-- Tabs -->
              <ul class="nav nav-tabs border-0 mb-3" id="createServiceTab" role="tablist">
                  <li class="" role="presentation">
                      <button class="nav-link active px-4 py-2 fw-bold" id="create-service-info-tab" data-bs-toggle="tab" data-bs-target="#create-service-info" type="button" role="tab" style="margin-left:1px;margin-bottom:-2px">Thông tin</button>
                  </li>
                  <li class="" role="presentation">
                      <button class="nav-link px-4 py-2 fw-bold" id="create-service-desc-tab" data-bs-toggle="tab" data-bs-target="#create-service-desc" type="button" role="tab" style="border-radius:8px;">Mô tả</button>
                  </li>
              </ul>
              <hr class="border-0 border-top border-secondary-subtle" style="background-color: black; height: 2px;margin-top:-14px">
              <div class="tab-content" id="createServiceTabContent">
                  <!-- Tab Thông tin -->
                  <div class="tab-pane fade show active" id="create-service-info" role="tabpanel">
                      <!-- THÔNG TIN CƠ BẢN -->
                      <div class="row mb-4">
                          <!-- Inputs bên trái -->
                          <div class="col-md-8">
                              <div class="row g-2">
                                  <div class="col-md-6">
                                      <label class="form-label">Mã dịch vụ</label>
                                      <input type="text" class="form-control" name="ma_hang" id="ma_hang" placeholder="Tự động">
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Nhóm dịch vụ <span class="text-danger">*</span></label>
                                      <select class="form-select" name="nhom_dich_vu_id" id="nhom_dich_vu_id" required>
                                          <option value="">Chọn nhóm dịch vụ (Bắt buộc)</option>
                                          @foreach($categories as $id => $name)
                                              <option value="{{ $id }}">{{ $name }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                                  <div class="col-md-12">
                                      <label class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                                      <input type="text" class="form-control" name="ten_dich_vu" id="ten_dich_vu" placeholder="Nhập tên dịch vụ" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Hình thức dịch vụ <span class="text-danger">*</span></label>
                                      <select class="form-select" name="hinh_thuc" id="hinh_thuc" required>
                                          <option value="">Chọn hình thức</option>
                                          <option value="tai_nha_thuoc">Tại nhà thuốc</option>
                                          <option value="tai_nha_khach">Tại nhà khách</option>
                                      </select>
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Thời gian thực hiện (phút)</label>
                                      <input type="number" class="form-control" name="thoi_gian_thuc_hien" id="thoi_gian_thuc_hien" placeholder="VD: 30" min="1">
                                  </div>
                              </div>
                          </div> 
                          <!-- Ảnh bên phải -->
                          <div class="col-md-4">
                              <!-- Chỉ 1 ảnh duy nhất - CÓ PREVIEW -->
                              <div class="border-2 border-dashed border-primary rounded-3 d-flex flex-column align-items-center justify-content-center position-relative bg-light" 
                                   style="width:200px;height:200px;background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="create-service-image-preview-container">
                                  <input type="file" name="image" accept="image/*" 
                                      style="opacity:0;position:absolute;width:100%;height:100%;cursor:pointer;top:0;left:0;z-index:1;"
                                      onchange="previewCreateServiceImage(this)">
                                  <div class="text-center" id="create-service-image-placeholder"> 
                                      <i class="fas fa-image fa-3x text-primary mb-3"></i>
                                      <div class="fw-bold text-primary mb-2">Thêm ảnh dịch vụ</div>
                                      <small class="text-muted">Click để chọn ảnh</small>
                                      <div class="mt-3">
                                          <span class="badge bg-light text-dark">Tối đa 2MB</span>
                                      </div>
                                  </div>
                                  <img id="create-service-image-preview" src="" alt="Preview" 
                                       style="width:100%;height:100%;object-fit:cover;border-radius:8px;display:none;">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Giá bán -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Giá bán</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-6">
                                  <label class="form-label">Chi phí thực hiện <span class="text-danger">*</span></label>
                                  <div class="input-group">
                                      <input type="text" class="form-control" name="gia_ban" id="gia_ban" value="0" required placeholder="VD: 120,000 hoặc 120000">
                                      <span class="input-group-text">đ</span>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                  <select class="form-select" name="trang_thai" id="trang_thai" required>
                                      <option value="kich_hoat">Kích hoạt</option>
                                      <option value="tam_ngung">Tạm ngưng</option>
                                  </select>
                              </div>
                          </div>
                      </fieldset>
                  </div>
                  
                  <!-- Tab Mô tả -->
                  <div class="tab-pane fade" id="create-service-desc" role="tabpanel">
                      <div class="mb-3">
                          <label for="mo_ta" class="form-label">Mô tả dịch vụ</label>
                          <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5" placeholder="Nhập mô tả chi tiết về dịch vụ..."></textarea>
                      </div>
                      <div class="mb-3">
                          <label for="ghi_chu" class="form-label">Ghi chú</label>
                          <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3" placeholder="Ghi chú thêm..."></textarea>
                      </div>
                  </div>
              </div>
          </div>
          
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-success">
                  <i></i> Lưu dịch vụ
              </button>
          </div>
        </form>
      </div>
    </div>
  </div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/create-modal.css') }}">
@endpush

@push('scripts')
<script>
// Function preview ảnh cho modal dịch vụ
function previewCreateServiceImage(input) {
    const preview = document.getElementById('create-service-image-preview');
    const placeholder = document.getElementById('create-service-image-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
}

// Format price input - allow comma and convert to number
document.getElementById('gia_ban').addEventListener('input', function(e) {
    let value = e.target.value;
    
    // Remove all non-numeric characters except comma
    value = value.replace(/[^\d,]/g, '');
    
    // Update the input value
    e.target.value = value;
});

// Convert price before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const priceInput = document.getElementById('gia_ban');
    let price = priceInput.value;
    
    // Convert comma-separated number to plain number
    price = price.replace(/,/g, '');
    
    // Validate it's a number
    if (isNaN(price) || price === '') {
        e.preventDefault();
        alert('Vui lòng nhập giá hợp lệ (chỉ số và dấu phẩy)');
        return false;
    }
    
    // Update the input value for submission
    priceInput.value = price;
});
</script>
@endpush --}}