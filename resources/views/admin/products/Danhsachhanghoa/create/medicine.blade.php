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
            @php
                if (!isset($errors)) {
                    $errors = new \Illuminate\Support\ViewErrorBag;
                }       
            @endphp   
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
                                    <div class="position-relative">
                                        <select class="form-select" name="drugusage_id" id="duong_dung_select" required onchange="handleCreateDrugRouteChange(this)">
                                            <option value="">Bắt buộc</option>
                                            @foreach($drugRoutes as $usage)
                                                <option value="{{ $usage->id }}">{{ $usage->name }}</option>
                                            @endforeach
                                            <option value="create_new">+ Tạo mới đường dùng</option>
                                        </select>
                                        
                                        <!-- Inline form cho Drug Route -->
                                        <div id="createDrugRouteInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                            <div class="mb-2">
                                                <input type="text" class="form-control" id="createNewDrugRouteName" placeholder="Nhập tên đường dùng mới">
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success" onclick="createNewCreateDrugRouteInline()">
                                                    <i class="fas fa-save"></i> Lưu
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="cancelCreateDrugRouteForm()">
                                                    <i class="fas fa-times"></i> Hủy
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>   
                                <div class="col-md-4">
                                    <label class="form-label">Hãng sản xuất<span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <select class="form-select" name="manufacturer_id" id="medicine_manufacturer_select" required onchange="handleMedicineManufacturerChange(this)">
                                            <option value="">Tìm hãng sản xuất</option>
                                            @foreach($manufacturers as $manu)
                                                <option value="{{ $manu->id }}">{{ $manu->name }}</option>
                                            @endforeach
                                            <option value="create_new">+ Tạo mới hãng sản xuất</option>
                                        </select>
                                        
                                        <!-- Inline form cho Manufacturer - MEDICINE -->
                                        <div id="createMedicineManufacturerInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                            <div class="mb-2">
                                                <input type="text" class="form-control" id="createNewMedicineManufacturerName" placeholder="Nhập tên hãng sản xuất mới">
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success" onclick="createNewMedicineManufacturerInline()">
                                                    <i class="fas fa-save"></i> Lưu
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="cancelMedicineManufacturerForm()">
                                                    <i class="fas fa-times"></i> Hủy
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
                                <div class="col-md-6">
                                    <label class="form-label">Định mức tồn thấp nhất</label>
                                    <input type="number" class="form-control" name="ton_thap_nhat" id="ton_thap_nhat" value="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Định mức tồn cao nhất</label>
                                    <input type="number" class="form-control" name="ton_cao_nhat" id="ton_cao_nhat" value="999999999">
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
                                            <select class="form-select" name="position_id" id="medicine_position_select" onchange="handleMedicinePositionChange(this)">
                                            <option value="">Chọn vị trí</option>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                            @endforeach
                                            <option value="create_new">+ Tạo mới vị trí</option>
                                        </select>                                     
                                            <!-- Inline form cho Position -->
                                        <div id="createMedicinePositionInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                            <div class="mb-2">
                                                <input type="text" class="form-control" id="createNewMedicinePositionName" placeholder="Nhập tên vị trí mới">
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success" onclick="createNewMedicinePositionInline()">
                                                    <i class="fas fa-save"></i> Lưu
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="cancelMedicinePositionForm()">
                                                    <i class="fas fa-times"></i> Hủy
                                                </button>
                                            </div>
                                        </div>
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
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm..."></textarea>
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
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/create-modal.css') }}">
    @endpush

    @push('scripts')
    <script>
    // Logic riêng cho modal thuốc
    function handleManufacturerChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value) {
            // Manufacturer selected
        }
    }

    // Function preview ảnh cho modal thuốc
    function previewCreateImage(input) {
        const preview = document.getElementById('create-image-preview');
        const placeholder = document.getElementById('create-image-placeholder');
        
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

    // Handle Drug Route Change - RIÊNG CHO THUỐC
    function handleCreateDrugRouteChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const inlineForm = document.getElementById('createDrugRouteInlineForm');
        
        if (selectedOption.value === 'create_new') {
            inlineForm.style.display = 'block';
            select.value = '';
        } else {
            inlineForm.style.display = 'none';
        }
    }

    // Create new drug route inline - RIÊNG CHO THUỐC
    function createNewCreateDrugRouteInline() {
        const nameInput = document.getElementById('createNewDrugRouteName');
        const name = nameInput.value.trim();
        
        if (!name) {
            alert('Vui lòng nhập tên đường dùng!');
            return;
        }
        
        // Gửi request tạo drug route mới
        fetch('{{ route("admin.products.drugroute.store") }}', {
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
                const select = document.getElementById('duong_dung_select');
                const newOption = new Option(data.drug_route.name, data.drug_route.id);
                select.add(newOption);
                select.value = data.drug_route.id;
                
                // Ẩn form inline
                document.getElementById('createDrugRouteInlineForm').style.display = 'none';
                nameInput.value = '';
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo đường dùng!');
        });
    }

    // Cancel drug route form - RIÊNG CHO THUỐC
    function cancelCreateDrugRouteForm() {
        document.getElementById('createDrugRouteInlineForm').style.display = 'none';
        document.getElementById('createNewDrugRouteName').value = '';
        document.getElementById('duong_dung_select').value = '';
    }

    // Handle Manufacturer Change - RIÊNG CHO THUỐC
    function handleMedicineManufacturerChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const inlineForm = document.getElementById('createMedicineManufacturerInlineForm');
        
        if (selectedOption.value === 'create_new') {
            inlineForm.style.display = 'block';
            select.value = '';
        } else {
            inlineForm.style.display = 'none';
        }
    }

    // Create new manufacturer inline - RIÊNG CHO THUỐC
    function createNewMedicineManufacturerInline() {
        const nameInput = document.getElementById('createNewMedicineManufacturerName');
        const name = nameInput.value.trim();
        
        if (!name) {
            alert('Vui lòng nhập tên hãng sản xuất!');
            return;
        }
        
        // Gửi request tạo manufacturer mới
        fetch('{{ route("admin.products.manufacturer.store") }}', {
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
                const select = document.getElementById('medicine_manufacturer_select');
                const newOption = new Option(data.manufacturer.name, data.manufacturer.id);
                select.add(newOption);
                select.value = data.manufacturer.id;
                
                // Ẩn form inline
                document.getElementById('createMedicineManufacturerInlineForm').style.display = 'none';
                nameInput.value = '';
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo hãng sản xuất!');
        });
    }

    // Cancel manufacturer form - RIÊNG CHO THUỐC
    function cancelMedicineManufacturerForm() {
        document.getElementById('createMedicineManufacturerInlineForm').style.display = 'none';
        document.getElementById('createNewMedicineManufacturerName').value = '';
        document.getElementById('medicine_manufacturer_select').value = '';
    }

    // Handle Position Change - RIÊNG CHO THUỐC
    function handleMedicinePositionChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const inlineForm = document.getElementById('createMedicinePositionInlineForm');
        
        if (selectedOption.value === 'create_new') {
            inlineForm.style.display = 'block';
            select.value = '';
        } else {
            inlineForm.style.display = 'none';
        }
    }

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

    // Open unit modal - RIÊNG CHO THUỐC
    function openCreateUnitModal() {
        const modalElement = document.getElementById('unitModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            alert('Modal đơn vị tính không tìm thấy!');
        }
    }
    </script>
    @endpush