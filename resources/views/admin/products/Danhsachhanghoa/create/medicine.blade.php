<!-- Modal tạo thuốc -->
    <div class="modal fade create-modal" id="createMedicineModal" tabindex="-1" aria-labelledby="createMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header">
            <h5 class="modal-title" id="createMedicineModalLabel">
                <i style="margin-right:10px"></i>Tạo thuốc 
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.medicines.store') }}" method="POST" enctype="multipart/form-data">
            @csrf   
            <div class="modal-body p-4">
                <!-- Tabs -->
                <ul class="nav nav-tabs border-0 mb-3" id="createMedicineTab" role="tablist">
                    <li class="" role="presentation">
                        <button class="nav-link active px-4 py-2 fw-bold" id="create-info-tab" data-bs-toggle="tab" data-bs-target="#create-info" type="button" role="tab" style="margin-left:1px;margin-bottom:-2px">Thông tin</button>
                    </li>
                    <li class="" role="presentation">
                        <button class="nav-link px-4 py-2 fw-bold" id="create-desc-tab" data-bs-toggle="tab" data-bs-target="#create-desc" type="button" role="tab" style="border-radius:8px;">Mô tả</button>
                    </li>
                </ul>
                <hr class="border-0 border-top border-secondary-subtle" style="background-color: black; height: 2px;margin-top:-14px">
                <div class="tab-content" id="createMedicineTabContent">
                    <!-- Tab Thông tin -->
                    <div class="tab-pane fade show active" id="create-info" role="tabpanel">
                        <!-- THÔNG TIN CƠ BẢN -->
                        <div class="row mb-4">
                            <!-- Inputs bên trái -->
                            <div class="col-md-8">
                                <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Mã hàng</label>
                                            <input type="text" class="form-control" name="ma_hang" id="ma_hang" placeholder="Tự động">
                                            @error('ma_hang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mã vạch</label>
                                        <input type="text" class="form-control" name="ma_vach" id="ma_vach" placeholder="Nhập mã vạch">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Tên thuốc <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ten_thuoc" id="ten_thuoc" placeholder="Nhập tên thuốc" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tên viết tắt</label>
                                        <input type="text" class="form-control" name="ten_viet_tat" id="ten_viet_tat" placeholder="Nhập tên viết tắt">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nhóm hàng <span class="text-danger">*</span></label>
                                        <select class="form-select" name="nhom_hang_id" id="nhom_hang_id" required>
                                            <option value="">Chọn nhóm hàng (Bắt buộc)</option>
                                            @foreach($categories as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div> 
                            <!-- Ảnh bên phải -->
                            <div class="col-md-4">
                                <!-- Chỉ 1 ảnh duy nhất - CÓ PREVIEW -->
                                <div class="border-2 border-dashed border-primary rounded-3 d-flex flex-column align-items-center justify-content-center position-relative bg-light" 
                                    style="width:200px;height:200px;background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="create-medicine-image-preview-container">
                                    <input type="file" name="image" accept="image/*" 
                                        style="opacity:0;position:absolute;width:100%;height:100%;cursor:pointer;top:0;left:0;z-index:1;"
                                        onchange="previewCreateMedicineImage(this)">
                                    <div class="text-center" id="create-medicine-image-placeholder"> 
                                        <i class="fas fa-image fa-3x text-primary mb-3"></i>
                                        <div class="fw-bold text-primary mb-2">Thêm ảnh sản phẩm</div>
                                        <small class="text-muted">Click để chọn ảnh</small>
                                        <div class="mt-3">
                                            <span class="badge bg-light text-dark">Tối đa 2MB</span>
                                        </div>
                                    </div>
                                    <img id="create-medicine-image-preview" src="" alt="Preview" 
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
                                    <input type="text" class="form-control" name="gia_von" id="gia_von" value="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="gia_ban" id="gia_ban" value="0" required>
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
                                    <input type="text" class="form-control" name="so_dang_ky" id="so_dang_ky" placeholder="Bắt buộc" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hoạt chất <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="hoat_chat" id="hoat_chat" placeholder="Bắt buộc" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hàm lượng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ham_luong" id="ham_luong" placeholder="Bắt buộc" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Đường dùng <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select" name="drugusage_id" id="duong_dung_select" required>
                                            <option value="">Bắt buộc</option>
                                            @foreach($drugRoutes as $usage)
                                                <option value="{{ $usage->id }}">{{ $usage->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" id="btnManageDrugRoute">
                                            <i class="fas fa-cog"></i> Quản lý
                                        </button>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:12px">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</div>
                                </div>   
                                <div class="col-md-4">
                                    <label class="form-label">Hãng sản xuất <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select" name="manufacturer_id" id="medicine_manufacturer_select" required>
                                            <option value="">Tìm hãng sản xuất</option>
                                            @foreach($manufacturers as $manu)
                                                <option value="{{ $manu->id }}">{{ $manu->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" id="btnManageManufacturer">
                                            <i class="fas fa-cog"></i> Quản lý
                                        </button>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:12px">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</div>
                                </div>                  
                                <div class="col-md-4">
                                    <label class="form-label">Nước sản xuất</label>
                                    <input type="text" class="form-control" name="nuoc_san_xuat" id="nuoc_san_xuat" placeholder="Tìm nước sản xuất">
                                </div>
                            </div>
                            <div class="row g-3 mb-2">
                                <div class="col-md-3">
                                    <label class="form-label">Quy cách đóng gói <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="quy_cach_dong_goi" id="quy_cach_dong_goi" placeholder="Bắt buộc" required>
                                </div>
                            </div>
                        </fieldset>
    
                        <!-- Tồn kho -->
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 fs-6">Tồn kho</legend>
                            <div class="row g-3 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label">Tồn kho</label>
                                    <input type="number" class="form-control" name="ton_kho" id="create_ton_kho" value="0" readonly>
                                    <small class="text-muted">Số lượng hiện có trong kho</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Định mức tồn thấp nhất</label>
                                    <input type="number" class="form-control" name="ton_thap_nhat" id="create_ton_thap_nhat" value="0">
                                    <small class="text-muted">Cảnh báo khi ≤ số này</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Định mức tồn cao nhất</label>
                                    <input type="number" class="form-control" name="ton_cao_nhat" id="create_ton_cao_nhat" value="999999999">
                                    <small class="text-muted">Cảnh báo khi ≥ số này</small>
                                </div>
                            </div>
                        </fieldset>
    
                        <!-- Vị trí, trọng lượng -->
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 fs-6">Vị trí, trọng lượng</legend>
                            <div class="row g-3 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Vị trí <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select" name="position_id" id="medicine_position_select" onchange="handleMedicinePositionChange(this)">
                                            <option value="">Chọn vị trí</option>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                            @endforeach
                                        </select>                                  
                                            <!-- Inline form cho Position -->
                                        <button class="btn btn-outline-secondary" type="button" id="btnManagePosition">
                                            <i class="fas fa-cog"></i> Quản lý
                                        </button>
                                        <div class="text-muted mt-1" style="font-size:12px">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Trọng lượng</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="trong_luong" id="trong_luong" value="0">
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
                                    <button type="button" class="btn btn-outline-primary w-100" onclick="openCreateUnitModal()">Thiết lập</button>
                                </div>
                            </div>
                        </fieldset>
    
                        <!-- Bán trực tiếp -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="ban_truc_tiep" name="ban_truc_tiep">
                            <label class="form-check-label" for="ban_truc_tiep">
                                Bán trực tiếp
                            </label>
                        </div>          
                    </div>
                    
                    <!-- Tab Mô tả -->
                    <div class="tab-pane fade" id="create-desc" role="tabpanel">
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả sản phẩm</label>
                            <textarea name="mo_ta" data-rte="true" data-rte-height="300"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success">
                    <i></i> Lưu thuốc
                </button>
            </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Modal Quản lý Đường dùng -->
    <div class="modal fade" id="manageDrugRouteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Quản lý đường dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <input type="text" id="manageDrugRouteSearch" class="form-control" placeholder="Tìm theo tên…">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" id="manageDrugRouteAddBtn">
                                <i class="fas fa-plus"></i> Thêm
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:90px">Mã</th>
                                    <th>Tên đường dùng</th>
                                    <th class="text-end" style="width:160px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="manageDrugRouteTbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Quản lý Hãng sản xuất -->
    <div class="modal fade" id="manageManufacturerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Quản lý hãng sản xuất</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <input type="text" id="manageManufacturerSearch" class="form-control" placeholder="Tìm theo tên…">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" id="manageManufacturerAddBtn">
                                <i class="fas fa-plus"></i> Thêm
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:90px">Mã</th>
                                    <th>Tên hãng sản xuất</th>
                                    <th class="text-end" style="width:160px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="manageManufacturerTbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Quản lý Vị trí -->
    <div class="modal fade" id="managePositionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Quản lý vị trí</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <input type="text" id="managePositionSearch" class="form-control" placeholder="Tìm theo tên…">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" id="managePositionAddBtn">
                                <i class="fas fa-plus"></i> Thêm
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:90px">Mã</th>
                                    <th>Tên vị trí</th>
                                    <th class="text-end" style="width:160px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="managePositionTbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/create-modal.css') }}">
    @endpush

    @push('scripts')
    <script>
    // Function preview ảnh cho modal thuốc
    function previewCreateMedicineImage(input) {
        const preview = document.getElementById('create-medicine-image-preview');
        const placeholder = document.getElementById('create-medicine-image-placeholder');
        
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

    // Drug Route Management - Modal-based approach
    let drugRoutes = @json($drugRoutes);
    
    const selectDrugRouteEl = document.getElementById('duong_dung_select');
    const btnManageDrugRoute = document.getElementById('btnManageDrugRoute');
    const manageDrugRouteModal = new bootstrap.Modal(document.getElementById('manageDrugRouteModal'));
    const manageDrugRouteTbody = document.getElementById('manageDrugRouteTbody');
    const manageDrugRouteSearch = document.getElementById('manageDrugRouteSearch');
    const manageDrugRouteAddBtn = document.getElementById('manageDrugRouteAddBtn');

    function nextDrugRouteId() { 
        return drugRoutes.length ? Math.max(...drugRoutes.map(r => r.id)) + 1 : 1; 
    }

    function syncDrugRouteSelect(selectedId = '') {
        selectDrugRouteEl.innerHTML = '';
        selectDrugRouteEl.appendChild(new Option('Bắt buộc', '', true, !selectedId));
        drugRoutes.forEach(r => selectDrugRouteEl.appendChild(new Option(r.name, r.id, false, r.id == selectedId)));
    }

    function renderDrugRouteManageTable(filter = '') {
        const q = (filter || '').toLowerCase().trim();
        const items = drugRoutes.filter(r => !q || r.name.toLowerCase().includes(q));
        manageDrugRouteTbody.innerHTML = items.map(r => `
            <tr data-id="${r.id}">
                <td><span class="badge text-bg-secondary">#${r.id}</span></td>
                <td class="name-cell">${r.name}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-edit"><i class="fas fa-edit"></i> Sửa</button>
                        <button class="btn btn-outline-danger btn-del"><i class="fas fa-trash"></i> Xóa</button>
                    </div>
                </td>
            </tr>`).join('') || `<tr><td colspan="3" class="text-center text-muted py-4">Không có mục nào.</td></tr>`;
    }

    function enterDrugRouteEditMode(row) {
        const id = +row.dataset.id;
        const item = drugRoutes.find(r => r.id === id);
        const cell = row.querySelector('.name-cell');
        cell.innerHTML = `
            <div class="d-flex gap-2">
                <input class="form-control form-control-sm edit-input" value="${item.name}">
                <button class="btn btn-success btn-sm btn-save"><i class="fas fa-check"></i></button>
                <button class="btn btn-light border btn-sm btn-cancel">Hủy</button>
            </div>`;
        
        cell.querySelector('.btn-save').addEventListener('click', () => {
            const val = cell.querySelector('.edit-input').value.trim();
            if(!val) return;
            if(drugRoutes.some(r => r.id !== id && r.name.toLowerCase() === val.toLowerCase()))
            {
                alert('Tên đường dùng đã tồn tại');
                return;
            }
            //nếu là item mới (có tên placeholder), gọi api tạo mới
            if(item.name === 'Nhập đường dùng mới')
            {
                fetch('{{ route("admin.products.drugroute.store") }}', {
                    method:'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data=> {
                    if(data.success) 
                    {
                        //cập nhật với id gửi từ server
                        const oldId = item.id; // Lưu lại ID tạm thời
                        item.id = data.drug_route.id;
                        item.name = data.drug_route.name;
                        
                        // Cập nhật lại mảng drugRoutes với ID mới
                        drugRoutes = drugRoutes.map(r => r.id === oldId ? item : r);
                        
                        console.log('Drug route created, old ID: ' + oldId + ', new ID: ' + item.id);
                        
                        renderDrugRouteManageTable(manageDrugRouteSearch.value);
                        syncDrugRouteSelect(data.drug_route.id);
                    }
                    else
                    {
                        console.error('Error:', data.message);
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    consol('Có lỗi xảy ra khi tạo đường dùng!');
                });
            } else {
                // Gọi API để update trong database
                fetch('{{ route("admin.products.drugroute.update", ":id") }}'.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        item.name = val;
                        renderDrugRouteManageTable(manageDrugRouteSearch.value);
                        syncDrugRouteSelect(+selectDrugRouteEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật đường dùng!');
                });
            }
        });

        cell.querySelector('.btn-cancel').addEventListener('click', () => {
            if(item.name === 'Nhập đường dùng mới')
            {
                drugRoutes = drugRoutes.filter(r => r.id !== id);
            }
            renderDrugRouteManageTable(manageDrugRouteSearch.value);
        });
    }

    // Event listeners for drug route management
    btnManageDrugRoute.addEventListener('click', () => {
        manageDrugRouteSearch.value = '';
        renderDrugRouteManageTable();
        manageDrugRouteModal.show();
    });

    manageDrugRouteSearch.addEventListener('input', e => renderDrugRouteManageTable(e.target.value));

    //js xử lý tạo mới đường dùng
    manageDrugRouteAddBtn.addEventListener('click', () => {
        const id = nextDrugRouteId();
        drugRoutes.push({id,name:'Nhập đường dùng mới'});
        renderDrugRouteManageTable(manageDrugRouteSearch.value);
        //tìm row vừa tạo và tự động vào edit
        const row = [...manageDrugRouteTbody.querySelectorAll('tr')].find(tr => +tr.dataset.id === id);
        if(row)
        {
            enterDrugRouteEditMode(row);
            setTimeout(() => {
                const input = row.querySelector('.edit-input');
                if(input)
                {
                    input.focus();
                    input.select();
                }
            },100);
        }
    });
    //js xử lý xóa đường dùng
    manageDrugRouteTbody.addEventListener('click', e => {
        const row = e.target.closest('tr'); 
        if (!row) return;
        
        if (e.target.closest('.btn-edit')) {
            enterDrugRouteEditMode(row);
        }
        
        if (e.target.closest('.btn-del')) {
            const id = +row.dataset.id;
            const item = drugRoutes.find(r => r.id === id);
            if (confirm(`Xóa "${item.name}"?`)) {
                // Gọi API để xóa khỏi database
                fetch('{{ route("admin.products.drugroute.destroy", ":id") }}'.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa khỏi array local
                        drugRoutes = drugRoutes.filter(r => r.id !== id);
                        renderDrugRouteManageTable(manageDrugRouteSearch.value);
                        if (+selectDrugRouteEl.value === id) selectDrugRouteEl.value = '';
                        syncDrugRouteSelect(+selectDrugRouteEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa đường dùng!');
                });
            }
        }
    });

    // Initialize drug route select
    syncDrugRouteSelect('');

    // Manufacturer Management - Modal-based approach
    let manufacturers = @json($manufacturers);
    
    const selectManufacturerEl = document.getElementById('medicine_manufacturer_select');
    const btnManageManufacturer = document.getElementById('btnManageManufacturer');
    const manageManufacturerModal = new bootstrap.Modal(document.getElementById('manageManufacturerModal'));
    const manageManufacturerTbody = document.getElementById('manageManufacturerTbody');
    const manageManufacturerSearch = document.getElementById('manageManufacturerSearch');
    const manageManufacturerAddBtn = document.getElementById('manageManufacturerAddBtn');

    function nextManufacturerId() { 
        return manufacturers.length ? Math.max(...manufacturers.map(m => m.id)) + 1 : 1; 
    }

    function syncManufacturerSelect(selectedId = '') {
        selectManufacturerEl.innerHTML = '';
        selectManufacturerEl.appendChild(new Option('Tìm hãng sản xuất', '', true, !selectedId));
        manufacturers.forEach(m => selectManufacturerEl.appendChild(new Option(m.name, m.id, false, m.id == selectedId)));
    }

    function renderManufacturerManageTable(filter = '') {
        const q = (filter || '').toLowerCase().trim();
        const items = manufacturers.filter(m => !q || m.name.toLowerCase().includes(q));
        manageManufacturerTbody.innerHTML = items.map(m => `
            <tr data-id="${m.id}">
                <td><span class="badge text-bg-secondary">#${m.id}</span></td>
                <td class="name-cell">${m.name}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-edit"><i class="fas fa-edit"></i> Sửa</button>
                        <button class="btn btn-outline-danger btn-del"><i class="fas fa-trash"></i> Xóa</button>
                    </div>
                </td>
            </tr>`).join('') || `<tr><td colspan="3" class="text-center text-muted py-4">Không có mục nào.</td></tr>`;
    }

    function enterManufacturerEditMode(row) {
        const id = +row.dataset.id;
        const item = manufacturers.find(m => m.id === id);
        const cell = row.querySelector('.name-cell');
        cell.innerHTML = `
            <div class="d-flex gap-2">
                <input class="form-control form-control-sm edit-input" value="${item.name}">
                <button class="btn btn-success btn-sm btn-save"><i class="fas fa-check"></i></button>
                <button class="btn btn-light border btn-sm btn-cancel">Hủy</button>
            </div>`;
        
        cell.querySelector('.btn-save').addEventListener('click', () => {
            const val = cell.querySelector('.edit-input').value.trim();
            if (!val) return;
            if (manufacturers.some(m => m.id !== id && m.name.toLowerCase() === val.toLowerCase())) {
                alert('Hãng sản xuất này đã tồn tại.'); 
                return;
            }
            
            // Nếu là item mới (có tên placeholder), gọi API tạo mới
            if (item.name === 'Nhập hãng sản xuất mới') {
                fetch('{{ route("admin.products.manufacturer.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật với ID thật từ server
                        const oldId = item.id; // Lưu lại ID tạm thời
                        item.id = data.manufacturer.id;
                        item.name = data.manufacturer.name;
                        
                        // Cập nhật lại mảng manufacturers với ID mới
                        manufacturers = manufacturers.map(m => m.id === oldId ? item : m);
                        
                        console.log('Manufacturer created, old ID: ' + oldId + ', new ID: ' + item.id);
                        
                        renderManufacturerManageTable(manageManufacturerSearch.value);
                        syncManufacturerSelect(data.manufacturer.id);
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tạo hãng sản xuất!');
                });
            } else {
                // Nếu là item đã tồn tại, gọi API update
                fetch('{{ route("admin.products.manufacturer.update", ":id") }}'.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        item.name = val;
                        renderManufacturerManageTable(manageManufacturerSearch.value);
                        syncManufacturerSelect(+selectManufacturerEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật hãng sản xuất!');
                });
            }
        });
        
        cell.querySelector('.btn-cancel').addEventListener('click', () => {
            // Nếu là item mới (có tên placeholder), xóa khỏi array
            if (item.name === 'Nhập hãng sản xuất mới') {
                manufacturers = manufacturers.filter(m => m.id !== id);
            }
            renderManufacturerManageTable(manageManufacturerSearch.value);
        });
    }

    // Event listeners for manufacturer management
    btnManageManufacturer.addEventListener('click', () => {
        manageManufacturerSearch.value = '';
        renderManufacturerManageTable();
        manageManufacturerModal.show();
    });

    manageManufacturerSearch.addEventListener('input', e => renderManufacturerManageTable(e.target.value));

    //js xử lý tạo mới hãng sản xuất
    manageManufacturerAddBtn.addEventListener('click', () => {
        // Tạo item mới với tên placeholder
        const id = nextManufacturerId();
        manufacturers.push({ id, name: 'Nhập hãng sản xuất mới' });
        renderManufacturerManageTable(manageManufacturerSearch.value);
        // Tìm row vừa tạo và tự động vào edit mode
        const row = [...manageManufacturerTbody.querySelectorAll('tr')].find(tr => +tr.dataset.id === id);
        if (row) {
            enterManufacturerEditMode(row);
            // Focus vào input field và select text
            setTimeout(() => {
                const input = row.querySelector('.edit-input');
                if (input) {
                    input.focus();
                    input.select();
                }
            }, 100);
        }
    });

    manageManufacturerTbody.addEventListener('click', e => {
        const row = e.target.closest('tr'); 
        if (!row) return;
        
        if (e.target.closest('.btn-edit')) {
            enterManufacturerEditMode(row);
        }
        
        if (e.target.closest('.btn-del')) {
            const id = +row.dataset.id;
            const item = manufacturers.find(m => m.id === id);
            if (confirm(`Xóa "${item.name}"?`)) {
                // Gọi API để xóa khỏi database
                fetch('{{ route("admin.products.manufacturer.destroy", ":id") }}'.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa khỏi array local
                        manufacturers = manufacturers.filter(m => m.id !== id);
                        renderManufacturerManageTable(manageManufacturerSearch.value);
                        if (+selectManufacturerEl.value === id) selectManufacturerEl.value = '';
                        syncManufacturerSelect(+selectManufacturerEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa hãng sản xuất!');
                });
            }
        }
    });

    // Initialize manufacturer select
    syncManufacturerSelect('');

    // Position Management - Inline form approach
    let positions = @json($positions);

    const selectPositionEl = document.getElementById('medicine_position_select');
    const btnManagePosition = document.getElementById('btnManagePosition');
    const managePositionModal = new bootstrap.Modal(document.getElementById('managePositionModal'));
    const managePositionTbody = document.getElementById('managePositionTbody');
    const managePositionSearch = document.getElementById('managePositionSearch');
    const managePositionAddBtn = document.getElementById('managePositionAddBtn');

    function nextPositionId()
    {
        return positions.length ? Math.max(...positions.map(p => p.id)) + 1 : 1;
    }

    function syncPositionSelect(selectedId = '')
    {
        selectPositionEl.innerHTML = '';
        selectPositionEl.appendChild(new Option('Chọn vị trí', '', true, !selectedId));
        positions.forEach(p => selectPositionEl.appendChild(new Option(p.name, p.id, false, p.id == selectedId)));
    }

    function renderPositionManageTable(filter = '')
    {
        const q = (filter || '').toLowerCase().trim();
        const items = positions.filter(r => !q || r.name.toLowerCase().includes(q));
        managePositionTbody.innerHTML = items.map(r => `
            <tr data-id="${r.id}">
                <td><span class="badge text-bg-secondary">#${r.id}</span></td>
                <td class="name-cell">${r.name}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-edit"><i class="fas fa-edit"></i> Sửa</button>
                        <button class="btn btn-outline-danger btn-del"><i class="fas fa-trash"></i> Xóa</button>
                    </div>
                </td>
            </tr>`).join('') || `<tr><td colspan="3" class="text-center text-muted py-4">Không có mục nào.</td></tr>`;
    }

    function enterPositionEditMode(row) {
        const id = +row.dataset.id;
        const item = positions.find(r => r.id === id);
        const cell = row.querySelector('.name-cell');
        cell.innerHTML = `
            <div class="d-flex gap-2">
                <input class="form-control form-control-sm edit-input" value="${item.name}">
                <button class="btn btn-success btn-sm btn-save"><i class="fas fa-check"></i></button>
                <button class="btn btn-light border btn-sm btn-cancel">Hủy</button>
            </div>`;
        
        cell.querySelector('.btn-save').addEventListener('click', () => {
            const val = cell.querySelector('.edit-input').value.trim();
            if(!val) return;
            if(positions.some(r => r.id !== id && r.name.toLowerCase() === val.toLowerCase()))
            {
                alert('Vị trí đã tồn tại');
                return;
            }
            //nếu là item mới (có tên placeholder), gọi api tạo mới
            if(item.name === 'Nhập vị trí mới')
            {
                fetch('{{ route("admin.products.position.store") }}', {
                    method:'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data=> {
                    if(data.success) 
                    {
                        //cập nhật với id gửi từ server
                        const oldId = item.id; // Lưu lại ID tạm thời
                        item.id = data.position.id; // Cập nhật ID từ server
                        item.name = data.position.name;
                        
                        // Cập nhật lại mảng positions với ID mới
                        positions = positions.map(p => p.id === oldId ? item : p);
                        
                        console.log('Position created, old ID: ' + oldId + ', new ID: ' + item.id);
                        
                        renderPositionManageTable(managePositionSearch.value);
                        syncPositionSelect(data.position.id);
                    }
                    else
                    {
                        console.error('Error:', data.message);
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Có lỗi xảy ra khi tạo vị trí!');
                });
            } else {
                // Gọi API để update trong database
                fetch('{{ route("admin.products.position.update", ":id") }}'.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        item.name = val;
                        renderPositionManageTable(managePositionSearch.value);
                        syncPositionSelect(+selectPositionEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật vị trí!');
                });
            }
        });

        cell.querySelector('.btn-cancel').addEventListener('click', () => {
            if(item.name === 'Nhập vị trí mới')
            {
                positions = positions.filter(r => r.id !== id);
            }
            renderPositionManageTable(managePositionSearch.value);
        });  
    }
    btnManagePosition.addEventListener('click', () => {
        managePositionSearch.value = '';
        renderPositionManageTable();
        managePositionModal.show();
    });

    managePositionSearch.addEventListener('input', e => renderPositionManageTable(e.target.value));

    managePositionAddBtn.addEventListener('click', () => {
        const id = nextPositionId();
        positions.push({id,name:'Nhập vị trí mới'});
        renderPositionManageTable(managePositionSearch.value);
        //tìm row vừa tạo và tự động vào edit
        const row = [...managePositionTbody.querySelectorAll('tr')].find(tr => +tr.dataset.id === id);
        if(row)
        {
            enterPositionEditMode(row);
            setTimeout(() => {
                const input = row.querySelector('.edit-input');
                if(input)
                {
                    input.focus();
                    input.select();
                }
            },100);
        }
    });

    managePositionTbody.addEventListener('click', e => {
        const row = e.target.closest('tr'); 
        if (!row) return;
        
        if (e.target.closest('.btn-edit')) {
            enterPositionEditMode(row);
        }
        
        if (e.target.closest('.btn-del')) {
            const id = +row.dataset.id;
            const item = positions.find(r => r.id === id);
            if (confirm(`Xóa "${item.name}"?`)) {
                // Gọi API để xóa khỏi database
                fetch('{{ route("admin.products.position.destroy", ":id") }}'.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa khỏi array local
                        positions = positions.filter(r => r.id !== id);
                        renderPositionManageTable(managePositionSearch.value);
                        if (+selectPositionEl.value === id) selectPositionEl.value = '';
                        syncPositionSelect(+selectPositionEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa vị trí!');
                });
            }
        }   
    });

    syncPositionSelect('');

    // Handle Position Change - RIÊNG CHO THUỐC
    // function handleMedicinePositionChange(select) {
    //     const selectedOption = select.options[select.selectedIndex];
    //     const inlineForm = document.getElementById('createMedicinePositionInlineForm');
        
    //     if (selectedOption.value === 'create_new') {
    //         inlineForm.style.display = 'block';
    //         select.value = '';
    //     } else {
    //         inlineForm.style.display = 'none';
    //     }
    // }

    // Create new position inline - RIÊNG CHO THUỐC
    function createNewMedicinePositionInline() {
        const nameInput = document.getElementById('createNewMedicinePositionName');
        const name = nameInput.value.trim();
        
        if (!name) {
            alert('Vui lòng nhập tên vị trí!');
            return;
        }
        
        // Gửi request tạo position mới
        fetch('{{ route("admin.products.position.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Thêm option mới vào select
                const select = document.getElementById('medicine_position_select');
                const newOption = new Option(data.position.name, data.position.id);
                select.add(newOption);
                select.value = data.position.id;
                
                // Ẩn form inline
                document.getElementById('createMedicinePositionInlineForm').style.display = 'none';
                nameInput.value = '';
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo vị trí!');
        });
    }

    // Cancel position form - RIÊNG CHO THUỐC
    function cancelMedicinePositionForm() {
        document.getElementById('createMedicinePositionInlineForm').style.display = 'none';
        document.getElementById('createNewMedicinePositionName').value = '';
        document.getElementById('medicine_position_select').value = '';
    }

    // Thiết lập đơn vị tính
    const setupUnitModal = new bootstrap.Modal(document.getElementById('setupUnitModal'));
    
    document.getElementById('btnSetupUnit').addEventListener('click', function() {
        setupUnitModal.show();
    });

    // Xử lý lưu đơn vị tính
    document.getElementById('saveUnitBtn').addEventListener('click', function() {
        const unitName = document.getElementById('unitName').value.trim();
        const unitDescription = document.getElementById('unitDescription').value.trim();
        
        if (!unitName) {
            alert('Vui lòng nhập tên đơn vị tính!');
            return;
        }
        
        // Cập nhật input đơn vị tính trong form chính
        document.getElementById('don_vi_tinh_input').value = unitName;
        
        // Đóng modal
        setupUnitModal.hide();
        
        // Reset form modal
        document.getElementById('unitName').value = '';
        document.getElementById('unitDescription').value = '';
        
        alert('Đã thiết lập đơn vị tính: ' + unitName);
    });

    </script>
    @endpush