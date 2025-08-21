<!-- Modal Tạo Nhà Cung Cấp -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createSupplierModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Thêm Nhà Cung Cấp Mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="createSupplierForm" action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Cột trái -->
                        <div class="col-md-6">
                            <!-- Tên nhà cung cấp -->
                            <div class="mb-3">
                                <label for="ten_nha_cung_cap" class="form-label">
                                    <i class="fas fa-building text-primary me-1"></i>Tên nhà cung cấp <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="ten_nha_cung_cap" name="ten_nha_cung_cap" required maxlength="255">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Mã nhà cung cấp -->
                            <div class="mb-3">
                                <label for="ma_nha_cung_cap" class="form-label">
                                    <i class="fas fa-barcode text-primary me-1"></i>Mã nhà cung cấp <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="ma_nha_cung_cap" name="ma_nha_cung_cap" required maxlength="50" placeholder="VD: NCC001">
                                <div class="form-text">Mã định danh duy nhất trong hệ thống</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Điện thoại -->
                            <div class="mb-3">
                                <label for="dien_thoai" class="form-label">
                                    <i class="fas fa-phone text-primary me-1"></i>Điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="dien_thoai" name="dien_thoai" required maxlength="20" placeholder="0123 456 789">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-primary me-1"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100" placeholder="supplier@example.com">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Nhóm nhà cung cấp -->
                            <div class="mb-3">
                                <label for="nhom_nha_cung_cap_id" class="form-label">
                                    <i class="fas fa-tags text-primary me-1"></i>Nhóm nhà cung cấp <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="nhom_nha_cung_cap_id" name="nhom_nha_cung_cap_id" required>
                                    <option value="">-- Chọn nhóm --</option>
                                    @foreach($supplierGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Cột phải -->
                        <div class="col-md-6">
                            <!-- Địa chỉ -->
                            <div class="mb-3">
                                <label for="dia_chi" class="form-label">
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>Địa chỉ chi tiết <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="dia_chi" name="dia_chi" rows="2" required placeholder="Số nhà, đường, phố..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Khu vực -->
                            <div class="mb-3">
                                <label for="khu_vuc" class="form-label">
                                    <i class="fas fa-map text-primary me-1"></i>Tỉnh/Thành phố <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="khu_vuc" name="khu_vuc" required>
                                    <option value="">-- Chọn tỉnh/thành --</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Phường/Xã -->
                            <div class="mb-3">  
                                <label for="phuong_xa" class="form-label">
                                    <i class="fas fa-location-arrow text-primary me-1"></i>Phường/Xã <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="phuong_xa" name="phuong_xa" required disabled>
                                    <option value="">-- Chọn tỉnh/thành trước --</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Tên công ty (xuất hóa đơn) -->
                            <div class="mb-3">
                                <label for="ten_cong_ty" class="form-label">
                                    <i class="fas fa-industry text-primary me-1"></i>Tên công ty (xuất hóa đơn)
                                </label>
                                <input type="text" class="form-control" id="ten_cong_ty" name="ten_cong_ty" maxlength="255" placeholder="Công ty TNHH ABC">
                                <div class="form-text">Tên chính thức cho việc xuất hóa đơn</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Mã số thuế -->
                            <div class="mb-3">
                                <label for="ma_so_thue" class="form-label">
                                    <i class="fas fa-file-invoice text-primary me-1"></i>Mã số thuế
                                </label>
                                <input type="text" class="form-control" id="ma_so_thue" name="ma_so_thue" maxlength="20" placeholder="0123456789">
                                <div class="form-text">MST duy nhất theo pháp luật</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Ghi chú -->
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ghi_chu" class="form-label">
                                    <i class="fas fa-sticky-note text-primary me-1"></i>Ghi chú
                                </label>
                                <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3" placeholder="Ghi chú bổ sung về nhà cung cấp..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="trang_thai" name="trang_thai" value="active" checked>
                                <label class="form-check-label" for="trang_thai">
                                    <i class="fas fa-check-circle text-success me-1"></i>Kích hoạt ngay
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Lưu nhà cung cấp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript xử lý form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createSupplierForm');
    
    form.addEventListener('submit', function(e) {
        // Clear previous validation
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Basic validation
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['ten_nha_cung_cap', 'ma_nha_cung_cap', 'dien_thoai', 'dia_chi', 'nhom_nha_cung_cap_id'];
        
        requiredFields.forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                field.nextElementSibling.textContent = 'Trường này là bắt buộc';
                isValid = false;
            }
        });
        
        // Phone validation
        const phone = form.querySelector('[name="dien_thoai"]');
        const phonePattern = /^[0-9+\-\s\(\)\.]{10,20}$/;
        if (phone.value && !phonePattern.test(phone.value)) {
            phone.classList.add('is-invalid');
            phone.nextElementSibling.textContent = 'Định dạng điện thoại không hợp lệ';
            isValid = false;
        }
        
        // Email validation
        const email = form.querySelector('[name="email"]');
        if (email.value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                email.classList.add('is-invalid');
                email.nextElementSibling.textContent = 'Định dạng email không hợp lệ';
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Reset form when modal closes
    const modal = document.getElementById('createSupplierModal');
    modal.addEventListener('hidden.bs.modal', function() {
        form.reset();
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('khu_vuc');
        const wardSelect = document.getElementById('phuong_xa');
            //danh sach tinh thành - sử dụng API provinces.open-api.vn
        async function loadProvinces() {
            try {               
                const response = await fetch('https://provinces.open-api.vn/api/?depth=1');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Dữ liệu tỉnh/thành:', data);
                
                // Clear existing options
                provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành --</option>';
                
                if (!Array.isArray(data) || data.length === 0) {
                    throw new Error('Không có dữ liệu tỉnh/thành');
                }
                
                // Add provinces to select
                data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.code;
                    option.textContent = province.name;
                    option.dataset.name = province.name; // Lưu tên để gửi lên server
                    provinceSelect.appendChild(option);
                });                               
            } 
        }
        //tải danh sách phường xã - sử dụng API provinces.open-api.vn
        async function loadWards(provinceCode) {
            try {
                console.log('Đang tải phường/xã cho tỉnh:', provinceCode);
                
                wardSelect.disabled = true;
                wardSelect.innerHTML = '<option value="">Đang tải...</option>';
                
                // Sử dụng API provinces.open-api.vn để lấy districts trước, sau đó lấy wards
                const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=3`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Dữ liệu tỉnh với districts và wards:', data);
                
                // Clear ward select first
                wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
                
                // Collect all wards from all districts in the province
                let allWards = [];
                if (data.districts && Array.isArray(data.districts)) {
                    data.districts.forEach(district => {
                        if (district.wards && Array.isArray(district.wards)) {
                            district.wards.forEach(ward => {
                                allWards.push({
                                    code: ward.code,
                                    name: `${ward.name} (${district.name})`, // Hiển thị cả tên quận/huyện
                                    ward_name: ward.name,
                                    district_name: district.name
                                });
                            });
                        }
                    });
                }
                
                if (allWards.length === 0) {
                    wardSelect.innerHTML = '<option value="">Không có dữ liệu phường/xã</option>';
                    console.warn('Không có phường/xã cho tỉnh:', provinceCode);
                } else {
                    // Sort wards by name
                    allWards.sort((a, b) => a.name.localeCompare(b.name));
                    
                    // Populate ward select
                    allWards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.code;
                        option.textContent = ward.name;
                        option.dataset.name = ward.ward_name; // Chỉ lưu tên phường/xã
                        wardSelect.appendChild(option);
                    });
                    
                    console.log(`Đã tải ${allWards.length} phường/xã thành công`);
                }
                
                wardSelect.disabled = false;
                
            } catch (error) {
                console.error('Lỗi load phường/xã:', error);
                wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                wardSelect.disabled = false;
                alert('Không thể tải danh sách phường/xã. Vui lòng thử lại!\nLỗi: ' + error.message);
            }
        }
               
        // Khi chọn tỉnh/thành
        provinceSelect.addEventListener('change', function() {
            const provinceCode = this.value;
            if (provinceCode) {
                loadWards(provinceCode);
            } else {
                wardSelect.disabled = true;
                wardSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành trước --</option>';
            }
        });
        // submit khi gửi dữ liệu
        const form = document.getElementById('createSupplierForm');
        form.addEventListener('submit', function(e) {
            // Lấy tên tỉnh/thành và phường/xã thay vì mã
            const selectedProvince = provinceSelect.options[provinceSelect.selectedIndex];
            const selectedWard = wardSelect.options[wardSelect.selectedIndex];
            
            if (selectedProvince && selectedProvince.dataset.name) {
                // Tạo hidden input để gửi tên tỉnh/thành
                let hiddenProvinceInput = document.getElementById('hidden_province_name');
                if (!hiddenProvinceInput) {
                    hiddenProvinceInput = document.createElement('input');
                    hiddenProvinceInput.type = 'hidden';
                    hiddenProvinceInput.id = 'hidden_province_name';
                    hiddenProvinceInput.name = 'khu_vuc_name';
                    form.appendChild(hiddenProvinceInput);
                }
                hiddenProvinceInput.value = selectedProvince.dataset.name;
            }
            
            if (selectedWard && selectedWard.dataset.name) {
                // Tạo hidden input để gửi tên phường/xã
                let hiddenWardInput = document.getElementById('hidden_ward_name');
                if (!hiddenWardInput) {
                    hiddenWardInput = document.createElement('input');
                    hiddenWardInput.type = 'hidden';
                    hiddenWardInput.id = 'hidden_ward_name';
                    hiddenWardInput.name = 'phuong_xa_name';
                    form.appendChild(hiddenWardInput);
                }
                hiddenWardInput.value = selectedWard.dataset.name;
            }
        });
        loadProvinces();
    });
</script>