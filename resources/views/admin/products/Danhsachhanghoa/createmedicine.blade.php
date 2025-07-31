<!-- Modal tạo thuốc -->
<div class="modal fade" id="createMedicineModal" tabindex="-1" aria-labelledby="createMedicineModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header">
        <h5 class="modal-title" id="createMedicineModalLabel">
          <i class="fas fa-plus-circle me-2"></i>Tạo thuốc mới
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.products.storeMedicine') }}" method="POST" enctype="multipart/form-data">
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
                <label class="form-label fw-bold mb-3">
                    <i class="fas fa-image me-2 text-primary"></i>Thêm ảnh sản phẩm
                </label>
                                 <!-- Chỉ 1 ảnh duy nhất - CÓ PREVIEW -->
                 <div class="border-2 border-dashed border-primary rounded-3 d-flex flex-column align-items-center justify-content-center position-relative bg-light" 
                      style="width:200px;height:200px;background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="image-preview-container">
                     <input type="file" name="image" accept="image/*" 
                         style="opacity:0;position:absolute;width:100%;height:100%;cursor:pointer;top:0;left:0;z-index:1;"
                         onchange="previewImage(this)">
                     <div class="text-center" id="image-placeholder"> 
                         <i class="fas fa-image fa-3x text-primary mb-3"></i>
                         <div class="fw-bold text-primary mb-2">Thêm ảnh sản phẩm</div>
                         <small class="text-muted">Click để chọn ảnh</small>
                         <div class="mt-3">
                             <span class="badge bg-light text-dark">Tối đa 2MB</span>
                         </div>
                     </div>
                     <img id="image-preview" src="" alt="Preview" 
                          style="width:100%;height:100%;object-fit:cover;border-radius:8px;display:none;">
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
                                <div class="position-relative">
                                    <select class="form-select" name="position_id" id="position_select" onchange="handlePositionChange(this)">
                                        <option value="">Chọn vị trí</option>
                                        @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                        @endforeach
                                        <option value="create_new">+ Tạo mới vị trí</option>
                                    </select>
                                    
                                    <!-- Inline form cho Position -->
                                    <div id="positionInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="newPositionName" placeholder="Nhập tên vị trí mới">
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-success" onclick="createNewPositionInline()">
                                                <i class="fas fa-save"></i> Lưu
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="cancelPositionForm()">
                                                <i class="fas fa-times"></i> Hủy
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                        <label for="mo_ta" class="form-label">Mô tả sản phẩm</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu sản phẩm
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Include Modal Components -->
@include('admin.products.Danhsachhanghoa.formmodal.unit_modal')

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

// Image preview function
function previewImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('image-placeholder');
    
    if (file) {
        // Kiểm tra kích thước file (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('File quá lớn! Vui lòng chọn ảnh nhỏ hơn 2MB.');
            input.value = '';
            return;
        }
        
        // Kiểm tra loại file
        if (!file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh!');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
}

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

// Position functions
function handlePositionChange(select) {
    if (select.value === 'create_new') {
        // Hiển thị inline form
        document.getElementById('positionInlineForm').style.display = 'block';
        document.getElementById('newPositionName').focus();
        select.value = ''; // Reset select
    }
}

function createNewPositionInline() {
    const name = document.getElementById('newPositionName').value.trim();
    if (!name) {
        alert('Vui lòng nhập tên vị trí!');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('{{ route("admin.products.position.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.position) {
            const select = document.getElementById('position_select');
            const newOption = document.createElement('option');
            newOption.value = data.position.id;
            newOption.textContent = data.position.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.position.id;
            
            // Ẩn form và reset
            cancelPositionForm();
            
            // Hiển thị thông báo đẹp
            showSuccessMessage('Tạo vị trí thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra khi tạo vị trí.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi mạng hoặc lỗi server.');
    });
}

function cancelPositionForm() {
    document.getElementById('positionInlineForm').style.display = 'none';
    document.getElementById('newPositionName').value = '';
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

</script>