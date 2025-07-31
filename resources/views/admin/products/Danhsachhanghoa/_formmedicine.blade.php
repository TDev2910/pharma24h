    @csrf
<div class="modal-body p-4">
    <!-- Tabs -->
    <ul class="nav nav-tabs border-0 mb-3" id="medicineTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" style="border-radius:8px;">Thông tin</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-bold" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab" style="border-radius:8px;">Mô tả</button>
        </li>
    </ul>
    <div class="tab-content" id="medicineTabContent">
        <!-- Tab Thông tin -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <!-- THÔNG TIN CƠ BẢN -->
            <div class="row mb-4">
            <!-- Inputs bên trái -->
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Mã hàng</label>
                        <input type="text" class="form-control" name="ma_hang" placeholder="Tự động">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mã vạch</label>
                        <input type="text" class="form-control" name="ma_vach" placeholder="Nhập mã vạch">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Tên thuốc <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ten_thuoc" placeholder="Nhập tên thuốc" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tên viết tắt</label>
                        <input type="text" class="form-control" name="ten_viet_tat" placeholder="Nhập tên viết tắt">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nhóm hàng <span class="text-danger">*</span></label>
                        <select class="form-select" name="nhom_hang_id" required>
                            <option value="">Chọn nhóm hàng (Bắt buộc)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div> 
    <!-- Ảnh bên phải -->
    <div class="col-md-4">
        <label class="form-label">Thêm ảnh</label>
        <div class="d-flex align-items-start gap-2">
            <!-- Khung upload chính -->
            <div class="border rounded d-flex flex-column align-items-center justify-content-center position-relative" 
                 style="width:180px;height:180px;background:#fafbfc;border:2px dashed #d1d5db;" id="preview-main">
                <input type="file" name="anh[]" multiple accept="image/*" 
                    style="opacity:0;position:absolute;width:100%;height:100%;cursor:pointer;top:0;left:0;z-index:1;"
                    onchange="handleImageUpload(this)" id="main-file-input">
                <div id="main-content"> 
                    <button type="button" class="btn btn-light btn-sm" style="pointer-events:none;">Thêm ảnh</button>
                    <small class="text-muted mt-1" style="font-size:11px;text-align:center;">Mỗi ảnh không quá 2 MB</small>
                </div>
            </div>
            
            <!-- Khung ảnh nhỏ -->
            <div id="preview-thumbs" class="d-flex flex-column gap-2">
                <!-- Sẽ được tạo bởi JavaScript -->
            </div>
        </div>
    </div>
    </div>
            <!-- Giá vốn, giá bán -->
            <fieldset class="mb-4 border rounded p-3">
                <legend class="float-none w-auto px-2 fs-6">Giá vốn, giá bán</legend>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Giá vốn <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="gia_von" value="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="gia_ban" value="0" required>
                            <button class="btn btn-outline-secondary" type="button">Thiết lập giá</button>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Thông tin thuốc -->
            <fieldset class="mb-4 border rounded p-3">
                <legend class="float-none w-auto px-2 fs-6">Thông tin thuốc</legend>
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Số đăng ký <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="so_dang_ky" placeholder="Bắt buộc" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hoạt chất <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="hoat_chat" placeholder="Bắt buộc" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hàm lượng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ham_luong" placeholder="Bắt buộc" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            Đường dùng <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <select class="form-select" name="drugusage_id" id="duong_dung_select" required onchange="handleDrugRouteChange(this)">
                                <option value="">Bắt buộc</option>
                                @foreach($drugRoutes as $usage)
                                    <option value="{{ $usage->id }}">{{ $usage->name }}</option>
                                @endforeach
                                <option value="create_new">+ Tạo mới đường dùng</option>
                            </select>
                            
                            <!-- Inline form cho Drug Route -->
                            <div id="drugRouteInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                <div class="mb-2">
                                    <input type="text" class="form-control" id="newDrugRouteName" placeholder="Nhập tên đường dùng mới">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" onclick="createNewDrugRouteInline()">
                                        <i class="fas fa-save"></i> Lưu
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="cancelDrugRouteForm()">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="col-md-4">
                        <label class="form-label">Hãng sản xuất<span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <select class="form-select" name="manufacturer" id="manufacturer_select" required onchange="handleManufacturerChange(this)">
                                <option value="">Tìm hãng sản xuất</option>
                                @foreach($manufacturers as $manu)
                                    <option value="{{ $manu->id }}">{{ $manu->name }}</option>
                                @endforeach
                                <option value="create_new">+ Tạo mới hãng sản xuất</option>
                            </select>
                            
                            <!-- Inline form cho Manufacturer -->
                            <div id="manufacturerInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                <div class="mb-2">
                                    <input type="text" class="form-control" id="newManufacturerName" placeholder="Nhập tên hãng sản xuất mới">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" onclick="createNewManufacturerInline()">
                                        <i class="fas fa-save"></i> Lưu
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="cancelManufacturerForm()">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>                  
                    <div class="col-md-4">
                        <label class="form-label">Nước sản xuất</label>
                        <input type="text" class="form-control" name="nuoc_san_xuat" placeholder="Tìm nước sản xuất">
                    </div>
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <label class="form-label">Quy cách đóng gói <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="quy_cach_dong_goi" placeholder="Bắt buộc" required>
                    </div>
                </div>
            </fieldset>

            <!-- Tồn kho -->
            <fieldset class="mb-4 border rounded p-3">
                <legend class="float-none w-auto px-2 fs-6">Tồn kho</legend>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Định mức tồn thấp nhất</label>
                        <input type="number" class="form-control" name="ton_thap_nhat" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Định mức tồn cao nhất</label>
                        <input type="number" class="form-control" name="ton_cao_nhat" value="999999999">
                    </div>
                </div>
            </fieldset>

            <!-- Vị trí, trọng lượng -->
            <fieldset class="mb-4 border rounded p-3">
                <legend class="float-none w-auto px-2 fs-6">Vị trí, trọng lượng</legend>
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Vị trí</label>
                        <select class="form-select" name="position_id" id="position_select">
                            <option value="">Chọn vị trí</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Trọng lượng</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="trong_luong" value="0">
                            <span class="input-group-text">g</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Thiết lập đơn vị tính -->
            <fieldset class="mb-4 border rounded p-3">
                <legend class="float-none w-auto px-2 fs-6">Thiết lập đơn vị tính</legend>
                <div class="row g-3 mb-2">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="don_vi_tinh" id="don_vi_tinh_input" placeholder="Nhập đơn vị tính" readonly>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-primary w-100" onclick="openUnitModal()">Thiết lập</button>
                    </div>
                </div>
            </fieldset>

            <!-- Bán trực tiếp -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="ban-truc-tiep" name="ban_truc_tiep">
                <label class="form-check-label" for="ban-truc-tiep">
                    Bán trực tiếp
                </label>
            </div>          
        </div>
        <!-- Tab Mô tả -->
        <div class="tab-pane fade" id="desc" role="tabpanel">
            <div class="mb-3">
                <label class="form-label">Mô tả chi tiết</label>
                <textarea class="form-control form-control-sm" rows="5" name="mo_ta" placeholder="Nhập mô tả chi tiết về thuốc..."></textarea>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
    <button type="submit" class="btn btn-success">Lưu</button>
    <button type="submit" class="btn btn-outline-success">Lưu & Tạo thêm hàng</button>
</div>



<!-- Modal Tạo hãng sản xuất -->
<div class="modal fade" id="createManufacturerModal" tabindex="-1" aria-labelledby="createManufacturerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
    <div class="modal-content" style="border-radius:12px; border:none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
      <div class="modal-header border-0 pb-2">
        <h5 class="modal-title fw-bold" id="createManufacturerModalLabel" style="color:#333; font-size:18px;">Tạo hãng sản xuất</h5>
        <button type="button" class="btn-close" onclick="closeManufacturerModal()" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 pt-2 pb-4">
        <form id="createManufacturerForm">
          @csrf
          <div class="mb-4">
            <label for="manufacturerName" class="form-label fw-medium mb-2" style="color:#555; font-size:14px;">Hãng sản xuất</label>
            <input type="text" class="form-control" id="manufacturerName" name="name" 
                   style="border:1px solid #e0e0e0; border-radius:8px; padding:12px 16px; font-size:14px; height:auto;"
                   placeholder="" required>
          </div>
          <div class="d-flex justify-content-end gap-3 mt-4">
            <button type="button" class="btn px-4 py-2" onclick="closeManufacturerModal()" 
                    style="background:#f5f5f5; color:#666; border:none; border-radius:8px; font-weight:500;">
              Bỏ qua
            </button>
            <button type="submit" class="btn px-4 py-2" 
                    style="background:#10b981; color:white; border:none; border-radius:8px; font-weight:500;">
              Lưu
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style> 
.nav-tabs {
    border-bottom: none !important;
}
.nav-tabs .nav-link {
    border: none !important;
    background: #fff !important;
    color: #333 !important;
    font-weight: bold;
}
</style>

<script>
// Image preview functionality
let uploadedImages = []; 
let currentMainIndex = 0; 

function handleImageUpload(input) {
    const files = Array.from(input.files);
    
    files.forEach(file => {
        if (file.type.startsWith('image/') && file.size <= 2 * 1024 * 1024) {
            const exists = uploadedImages.some(img => 
                img.file.name === file.name && img.file.size === file.size
            );
            
            if (!exists) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadedImages.push({
                        file: file,
                        dataUrl: e.target.result,
                        id: Date.now() + Math.random()
                    });
                    
                    if (uploadedImages.length === 1) {
                        currentMainIndex = 0;
                    }
                    
                    updateImageDisplay();
                };
                reader.readAsDataURL(file);
            }
        } else if (file.size > 2 * 1024 * 1024) {
            alert(`File "${file.name}" quá lớn! Vui lòng chọn ảnh nhỏ hơn 2MB.`);
        }
    });
    
    input.value = '';
}

function updateImageDisplay() {
    const mainDiv = document.getElementById('preview-main');
    const thumbsDiv = document.getElementById('preview-thumbs');
    
    if (uploadedImages.length > 0) {
        const mainImage = uploadedImages[currentMainIndex];
        const mainContent = document.getElementById('main-content');
        mainContent.innerHTML = `
            <img src="${mainImage.dataUrl}" 
                style="width:100%;height:100%;object-fit:cover;border-radius:4px;position:absolute;top:0;left:0;" 
                alt="Main Preview">
            <button type="button" class="btn btn-sm btn-danger position-absolute" 
                style="top:5px;right:5px;width:25px;height:25px;padding:0;line-height:1;z-index:10;"
                onclick="removeImage(${currentMainIndex})">×</button>
        `;
    } else {
        document.getElementById('main-content').innerHTML = `
            <button type="button" class="btn btn-light btn-sm" style="pointer-events:none;">Thêm ảnh</button>
            <small class="text-muted mt-1" style="font-size:11px;text-align:center;">Mỗi ảnh không quá 2 MB</small>
        `;
        currentMainIndex = 0;
    }
    
    thumbsDiv.innerHTML = '';
    
    for (let i = 0; i < 4; i++) {
        if (i < uploadedImages.length) {
            const image = uploadedImages[i];
            const isActive = i === currentMainIndex;
            
            thumbsDiv.innerHTML += `
                <div class="border rounded d-flex align-items-center justify-content-center position-relative" 
                     style="width:40px;height:40px;background:#fafbfc;border:${isActive ? '2px solid #007bff' : '1px solid #e5e7eb'};cursor:pointer;overflow:hidden;"
                     onclick="setMainImage(${i})" title="Click để xem lớn">
                    <img src="${image.dataUrl}" style="width:100%;height:100%;object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute" 
                            style="top:-2px;right:-2px;width:16px;height:16px;padding:0;line-height:1;font-size:10px;"
                            onclick="removeImage(${i}); event.stopPropagation();">×</button>
                </div>
            `;
        } else {
            thumbsDiv.innerHTML += `
                <div class="border rounded d-flex align-items-center justify-content-center" 
                     style="width:40px;height:40px;background:#fafbfc;border:1px solid #e5e7eb;cursor:pointer;"
                     onclick="document.getElementById('main-file-input').click()">
                    <i class="fa fa-plus text-muted" style="font-size:12px;"></i>
                </div>
            `;
        }
    }
    
    updateFormData();
}

function setMainImage(index) {
    if (index < uploadedImages.length) {
        currentMainIndex = index;
        updateImageDisplay();
    }
}

function removeImage(index) {
    if (confirm('Bạn có chắc muốn xóa ảnh này?')) {
        uploadedImages.splice(index, 1);
        
        if (currentMainIndex >= uploadedImages.length && uploadedImages.length > 0) {
            currentMainIndex = uploadedImages.length - 1;
        } else if (uploadedImages.length === 0) {
            currentMainIndex = 0;
        }
        
        updateImageDisplay();
    }
}

function updateFormData() {
    const existingInputs = document.querySelectorAll('input[name="uploaded_images[]"]');
    existingInputs.forEach(input => input.remove());
    
    const mainDiv = document.getElementById('preview-main');
    
    uploadedImages.forEach((image, index) => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'uploaded_images[]';
        hiddenInput.value = image.dataUrl; 
        mainDiv.appendChild(hiddenInput);
    });
}

// Combobox handler
// Inline form handlers
function handleDrugRouteChange(select) {
    if (select.value === 'create_new') {
        // Hiển thị inline form
        document.getElementById('drugRouteInlineForm').style.display = 'block';
        document.getElementById('newDrugRouteName').focus();
        select.value = ''; // Reset select
    }
}

function createNewDrugRouteInline() {
    const name = document.getElementById('newDrugRouteName').value.trim();
    if (!name) {
        alert('Vui lòng nhập tên đường dùng!');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('{{ route("admin.products.drugroute.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.drug_route) {
            const select = document.getElementById('duong_dung_select');
            const newOption = document.createElement('option');
            newOption.value = data.drug_route.id;
            newOption.textContent = data.drug_route.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.drug_route.id;
            
            // Ẩn form và reset
            cancelDrugRouteForm();
            
            // Hiển thị thông báo đẹp
            showSuccessMessage('Tạo đường dùng thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra khi tạo đường dùng.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi mạng hoặc lỗi server.');
    });
}

function cancelDrugRouteForm() {
    document.getElementById('drugRouteInlineForm').style.display = 'none';
    document.getElementById('newDrugRouteName').value = '';
}

function handleManufacturerChange(select) {
    if (select.value === 'create_new') {
        // Hiển thị inline form
        document.getElementById('manufacturerInlineForm').style.display = 'block';
        document.getElementById('newManufacturerName').focus();
        select.value = ''; // Reset select
    }
}

function createNewManufacturerInline() {
    const name = document.getElementById('newManufacturerName').value.trim();
    if (!name) {
        alert('Vui lòng nhập tên hãng sản xuất!');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('{{ route("admin.products.manufacturer.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.manufacturer) {
            const select = document.getElementById('manufacturer_select');
            const newOption = document.createElement('option');
            newOption.value = data.manufacturer.id;
            newOption.textContent = data.manufacturer.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.manufacturer.id;
            
            // Ẩn form và reset
            cancelManufacturerForm();
            
            // Hiển thị thông báo đẹp
            showSuccessMessage('Tạo hãng sản xuất thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra khi tạo hãng sản xuất.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi mạng hoặc lỗi server.');
    });
}

function cancelManufacturerForm() {
    document.getElementById('manufacturerInlineForm').style.display = 'none';
    document.getElementById('newManufacturerName').value = '';
}

// Hiển thị thông báo đẹp
function showSuccessMessage(message) {
    // Tạo toast notification
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast.querySelector('.toast'));
    bsToast.show();
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Form handlers
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    updateImageDisplay();
});

// Unit Modal Functions
function openUnitModal() {
    const modal = new bootstrap.Modal(document.getElementById('unitModal'));
    modal.show();
}

function closeUnitModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('unitModal'));
    if (modal) {
        modal.hide();
    }
}

function saveUnit() {
    const unitName = document.getElementById('unitName').value.trim();
    const unitPrice = document.getElementById('unitPrice').value;
    const directSale = document.getElementById('directSale').checked;
    
    if (!unitName) {
        alert('Vui lòng nhập tên đơn vị cơ bản!');
        return;
    }
    
    // Cập nhật input field
    document.getElementById('don_vi_tinh_input').value = unitName;
    
    // Có thể lưu thêm thông tin vào hidden fields
    document.getElementById('unit_price_hidden').value = unitPrice;
    document.getElementById('direct_sale_hidden').value = directSale ? '1' : '0';
    
    // Đóng modal
    closeUnitModal();
    
    // Hiển thị thông báo
    showSuccessMessage('Thiết lập đơn vị tính thành công!');
}

// Initialize unit modal
document.addEventListener('DOMContentLoaded', function() {
    const unitForm = document.getElementById('unitForm');
    if (unitForm) {
        unitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveUnit();
        });
    }
});
</script>

<!-- Modal Thêm đơn vị cơ bản -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="unitModalLabel">
                    Thêm đơn vị cơ bản
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="unitForm">
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Đơn vị cơ bản là đơn vị bán phổ biến nhất hoặc đơn vị chính dùng để quản lý tồn kho
                    </p>
                    
                    <div class="mb-3">
                        <label for="unitName" class="form-label">Tên đơn vị cơ bản</label>
                        <input type="text" class="form-control" id="unitName" placeholder="Tên đơn vị cơ bản" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="unitPrice" class="form-label">Giá bán</label>
                        <input type="number" class="form-control" id="unitPrice" value="0" min="0" step="0.01">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="directSale" checked>
                        <label class="form-check-label" for="directSale">
                            Bán trực tiếp
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
                    <button type="submit" class="btn btn-success">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden fields để lưu thêm thông tin -->
<input type="hidden" id="unit_price_hidden" name="unit_price" value="0">
<input type="hidden" id="direct_sale_hidden" name="direct_sale" value="1">

