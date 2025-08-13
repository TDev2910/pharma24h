<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">
                    <i></i>Chỉnh sửa Dịch vụ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editServiceForm" action="{{ route('admin.services.update', $service->id ?? 0) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="editServiceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="edit-basic-info-tab" data-bs-toggle="tab" 
                                    data-bs-target="#edit-basic-info" type="button" role="tab">
                                <p>Thông tin cơ bản</p>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-service-details-tab" data-bs-toggle="tab" 
                                    data-bs-target="#edit-service-details" type="button" role="tab">
                                <p>Chi tiết dịch vụ</p>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-service-image-tab" data-bs-toggle="tab" 
                                    data-bs-target="#edit-service-image" type="button" role="tab">
                                <p>Hình ảnh & Ghi chú</p>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3" id="editServiceTabContent">
                        <!-- Basic Information Tab -->
                        <div class="tab-pane fade show active" id="edit-basic-info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_ma_dich_vu" class="form-label">
                                            Mã dịch vụ <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="service_edit_ma_dich_vu" 
                                               name="ma_dich_vu" required 
                                               value="{{ $service->ma_dich_vu }}"
                                               placeholder="Nhập mã dịch vụ (VD: DV001)">
                                        <div class="form-text">Mã dịch vụ phải là duy nhất</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_ten_dich_vu" class="form-label">
                                            Tên dịch vụ <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="service_edit_ten_dich_vu" 
                                               name="ten_dich_vu" required 
                                               value="{{ $service->ten_dich_vu }}"
                                               placeholder="Nhập tên dịch vụ">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Nhóm dịch vụ <span class="text-danger">*</span></label>
                                    <select class="form-select" name="nhom_dich_vu_id" id="nhom_dich_vu_id" required>
                                        <option value="">Chọn nhóm dịch vụ (Bắt buộc)</option>
                                        @foreach($categories as $id => $name)
                                            <option value="{{ $id }}" {{ ($service->nhom_dich_vu_id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_gia_ban" class="form-label">
                                            Chi phí thực hiện <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="service_edit_gia_ban" 
                                                   name="gia_ban" min="0" step="1000" required 
                                                   value="{{ $service->gia_ban }}"
                                                   placeholder="0">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_hinh_thuc" class="form-label">
                                            Hình thức dịch vụ <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="service_edit_hinh_thuc" name="hinh_thuc" required>
                                            <option value="tai_nha_thuoc" {{ ($service->hinh_thuc == 'tai_nha_thuoc') ? 'selected' : '' }}>Tại nhà thuốc</option>
                                            <option value="tai_nha_khach" {{ ($service->hinh_thuc == 'tai_nha_khach') ? 'selected' : '' }}>Tại nhà khách</option>
                                        </select>
                                        <div class="form-text">
                                            <i></i>
                                            Tại nhà thuốc: Khách đến cửa hàng | Tại nhà khách: Nhân viên đến tận nơi
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_trang_thai" class="form-label">
                                            Trạng thái <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="service_edit_trang_thai" name="trang_thai" required>
                                            <option value="kich_hoat" {{ ($service->trang_thai == 'kich_hoat') ? 'selected' : '' }}>Kích hoạt</option>
                                            <option value="tam_ngung" {{ ($service->trang_thai == 'tam_ngung') ? 'selected' : '' }}>Tạm ngưng</option>
                                        </select>
                                        <div class="form-text">
                                            <span class="badge bg-success me-1">Kích hoạt</span>
                                            <span class="badge bg-warning">Tạm ngưng</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  

                        <!-- Service Details Tab -->
                        <div class="tab-pane fade" id="edit-service-details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_thoi_gian_thuc_hien" class="form-label">
                                            Thời gian thực hiện (phút)
                                        </label>
                                        <input type="number" class="form-control" id="service_edit_thoi_gian_thuc_hien" 
                                            name="thoi_gian_thuc_hien" min="1" max="480" 
                                            value="{{ $service->thoi_gian_thuc_hien }}"
                                            placeholder="VD: 30">
                                        <div class="form-text">Thời gian ước tính để hoàn thành dịch vụ</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Thông tin bổ sung</label>
                                        <div class="border rounded p-3 bg-light">
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Ngày tạo:</small><br>
                                                    <span id="edit_created_at">-</span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Cập nhật lần cuối:</small><br>
                                                    <span id="edit_updated_at">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="service_edit_mo_ta" class="form-label">Mô tả dịch vụ</label>
                                <textarea class="form-control" id="service_edit_mo_ta" name="mo_ta" rows="4" 
                                          placeholder="Mô tả chi tiết về dịch vụ:
- Quy trình thực hiện
- Lợi ích cho khách hàng  
- Điều kiện áp dụng
- Lưu ý đặc biệt...">{{ $service->mo_ta }}</textarea>
                                <div class="form-text">Mô tả chi tiết giúp khách hàng hiểu rõ hơn về dịch vụ</div>
                            </div>

                            <!-- Service Process Steps -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i></i>Quy trình thực hiện (tham khảo)
                                </label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">
                                                <i></i>Tại nhà thuốc:
                                            </h6>
                                            <ol class="small text-muted">
                                                <li>Khách hàng đến cửa hàng</li>
                                                <li>Nhân viên tư vấn và thực hiện</li>
                                                <li>Ghi nhận kết quả</li>
                                                <li>Xuất hóa đơn thanh toán</li>
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-info">
                                                <i></i>Tại nhà khách:
                                            </h6>
                                            <ol class="small text-muted">
                                                <li>Đặt lịch hẹn trước</li>
                                                <li>Nhân viên đến tận nơi</li>
                                                <li>Thực hiện dịch vụ tại nhà</li>
                                                <li>Thanh toán và ghi nhận</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image & Notes Tab -->
                        <div class="tab-pane fade" id="edit-service-image" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_image" class="form-label">Hình ảnh minh họa</label>
                                        <input type="file" class="form-control" id="service_edit_image" 
                                               name="image" accept="image/*" 
                                               onchange="previewEditServiceImage(this)">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Chọn hình ảnh mới để thay thế (JPG, PNG, GIF - tối đa 2MB)
                                        </div>
                                    </div>
                                    
                                    <!-- Current Image -->
                                    <div id="editCurrentImage" class="text-center mb-3" style="display: none;">
                                        <label class="form-label">Hình ảnh hiện tại:</label>
                                        <div>
                                            <img id="editCurrentImg" src="" alt="Current Image" 
                                                 class="img-fluid border rounded" 
                                                 style="max-height: 150px; max-width: 100%;">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="removeEditCurrentImage()">
                                                <i class="fas fa-times me-1"></i>Xóa hình ảnh hiện tại
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- New Image Preview -->
                                    <div id="editImagePreview" class="text-center" style="display: none;">
                                        <label class="form-label">Hình ảnh mới:</label>
                                        <div>
                                            <img id="editPreviewImg" src="" alt="Preview" 
                                                 class="img-fluid border rounded" 
                                                 style="max-height: 200px; max-width: 100%;">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="removeEditImagePreview()">
                                                <i class="fas fa-times me-1"></i>Hủy hình ảnh mới
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_edit_ghi_chu" class="form-label">Ghi chú</label>
                                        <textarea class="form-control" id="service_edit_ghi_chu" name="ghi_chu" rows="5" 
                                                  placeholder="Ghi chú thêm:
                                            - Điều kiện áp dụng
                                            - Khuyến mãi đặc biệt
                                            - Lưu ý quan trọng
                                            - Thông tin liên hệ...">{{ $service->ghi_chu }}
                                        </textarea>
                                    </div>

                                    <!-- Service Stats -->
                                    <div class="alert alert-success">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-chart-line me-1"></i>Thống kê dịch vụ:
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold text-primary" id="edit_total_bookings">0</div>
                                                <small>Lượt đặt</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-success" id="edit_total_revenue">0đ</div>
                                                <small>Doanh thu</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-info" id="edit_avg_rating">0</div>
                                                <small>Đánh giá</small>
                                            </div>
                                        </div>
                                        <div class="form-text text-center mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Thống kê sẽ được cập nhật khi có booking dịch vụ
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i></i>Cập nhật dịch vụ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/**
 * Preview selected image for edit service
 */
function previewEditServiceImage(input) {
    const preview = document.getElementById('editImagePreview');
    const previewImg = document.getElementById('editPreviewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

/**
 * Remove image preview for edit service
 */
function removeEditImagePreview() {
    const input = document.getElementById('service_edit_image');
    const preview = document.getElementById('editImagePreview');
    
    input.value = '';
    preview.style.display = 'none';
}

/**
 * Remove current image (will be handled in backend)
 */
function removeEditCurrentImage() {
    if (confirm('Bạn có chắc chắn muốn xóa hình ảnh hiện tại?')) {
        const currentImage = document.getElementById('editCurrentImage');
        currentImage.style.display = 'none';
        
        // Add hidden input to mark image for deletion
        const form = document.getElementById('editServiceForm');
        let deleteImageInput = form.querySelector('input[name="delete_image"]');
        if (!deleteImageInput) {
            deleteImageInput = document.createElement('input');
            deleteImageInput.type = 'hidden';
            deleteImageInput.name = 'delete_image';
            deleteImageInput.value = '1';
            form.appendChild(deleteImageInput);
        }
    }
}

/**
 * Reset edit service form when modal is closed
 */
document.getElementById('editServiceModal').addEventListener('hidden.bs.modal', function () {
    // Reset form
    document.getElementById('editServiceForm').reset();
    
    // Hide image previews
    document.getElementById('editCurrentImage').style.display = 'none';
    document.getElementById('editImagePreview').style.display = 'none';
    
    // Remove delete image input if exists
    const deleteImageInput = document.querySelector('input[name="delete_image"]');
    if (deleteImageInput) {
        deleteImageInput.remove();
    }
    
    // Reset to first tab
    const firstTab = document.getElementById('edit-basic-info-tab');
    const firstTabPane = document.getElementById('edit-basic-info');
    
    // Remove active class from all tabs and panes
    document.querySelectorAll('#editServiceTabs .nav-link').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('#editServiceTabContent .tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
    });
    
    // Activate first tab
    firstTab.classList.add('active');
    firstTabPane.classList.add('show', 'active');
});

/**
 * Populate edit form with service data (called from service-management.js)
 */
function populateEditServiceForm(service) {
    // Show current image if exists
    if (service.image) {
        const currentImage = document.getElementById('editCurrentImage');
        const currentImg = document.getElementById('editCurrentImg');
        currentImg.src = `/storage/${service.image}`;
        currentImage.style.display = 'block';
    }
    
    // Update timestamps
    document.getElementById('edit_created_at').textContent = 
        service.created_at ? new Date(service.created_at).toLocaleString('vi-VN') : '-';
    document.getElementById('edit_updated_at').textContent = 
        service.updated_at ? new Date(service.updated_at).toLocaleString('vi-VN') : '-';
    
    // Update stats (placeholder - will be implemented later)
    document.getElementById('edit_total_bookings').textContent = '0';
    document.getElementById('edit_total_revenue').textContent = '0đ';
    document.getElementById('edit_avg_rating').textContent = '0';
}
</script>
