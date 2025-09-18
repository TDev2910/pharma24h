<!-- Modal Thêm Khách Hàng -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Chỉnh sửa thông tin khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit-user-id" name="user_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-name" name="name" required>
                                <div class="invalid-feedback" id="edit-name-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit-email" name="email" required>
                                <div class="invalid-feedback" id="edit-email-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="edit-password" name="password" placeholder="Để trống nếu không đổi">
                                <div class="invalid-feedback" id="edit-password-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="edit-password_confirmation" name="password_confirmation" placeholder="Để trống nếu không đổi">
                                <div class="invalid-feedback" id="edit-password-confirmation-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-phone" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="edit-phone" name="phone">
                                <div class="invalid-feedback" id="edit-phone-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-address" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="edit-address" name="address">
                                <div class="invalid-feedback" id="edit-address-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Details Section -->
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ chi tiết (tùy chọn)</label>
                        <div class="address-details bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Tỉnh/Thành phố</label>
                                        <select id="edit-province" name="province" class="form-control address-select">
                                            <option value="">-- Chọn tỉnh/thành phố --</option>
                                            <!-- Option sẽ được load bằng JS -->
                                        </select>
                                        <div class="invalid-feedback" id="edit-province-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Quận/Huyện</label>
                                        <select id="edit-district" name="district" class="form-control address-select" disabled>
                                            <option value="">-- Chọn quận/huyện --</option>
                                        </select>
                                        <div class="invalid-feedback" id="edit-district-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Xã/Phường</label>
                                        <select id="edit-ward" name="ward" class="form-control address-select" disabled>
                                            <option value="">-- Chọn xã/phường --</option>
                                        </select>
                                        <div class="invalid-feedback" id="edit-ward-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdateUser">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditUserModal(user) {
    try {
        console.log('openEditUserModal called with:', user);
        
        // Điền dữ liệu vào form
        document.getElementById('edit-user-id').value = user.id || '';
        document.getElementById('edit-name').value = user.name || '';
        document.getElementById('edit-email').value = user.email || '';
        document.getElementById('edit-phone').value = user.phone || '';
        document.getElementById('edit-address').value = user.address || '';
        
        // Cập nhật action URL
        document.getElementById('editUserForm').action = '/admin/customers/' + user.id;
        
        // Load provinces và set current values
        loadEditProvinces(user.province || '', user.district || '', user.ward || '');
        
        // Mở modal
        var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();
        
        console.log('Modal opened successfully');
    } catch (error) {
        console.error('Error in openEditUserModal:', error);
        alert('Có lỗi xảy ra khi mở modal: ' + error.message);
    }
}

function loadEditProvinces(currentProvince = '', currentDistrict = '', currentWard = '') {
    fetch('https://provinces.open-api.vn/api/?depth=1')
        .then(response => response.json())
        .then(data => {
            const provinceSelect = document.getElementById('edit-province');
            provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
            
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                if (province.code === currentProvince) {
                    option.selected = true;
                }
                provinceSelect.appendChild(option);
            });

            // Load districts if province is selected
            if (currentProvince) {
                loadEditDistricts(currentProvince, currentDistrict, currentWard);
            }
        })
        .catch(error => {
            console.error('Error loading provinces:', error);
            document.getElementById('edit-province').innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        });
}

function loadEditDistricts(provinceCode, currentDistrict = '', currentWard = '') {
    const districtSelect = document.getElementById('edit-district');
    const wardSelect = document.getElementById('edit-ward');

    districtSelect.innerHTML = '<option value="">Đang tải...</option>';
    districtSelect.disabled = true;
    wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
    wardSelect.disabled = true;

    if (!provinceCode) {
        districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
        return;
    }

    fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
            districtSelect.disabled = false;
            districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
            
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.code;
                option.textContent = district.name;
                if (district.code === currentDistrict) {
                    option.selected = true;
                }
                districtSelect.appendChild(option);
            });

            // Load wards if district is selected
            if (currentDistrict) {
                loadEditWards(currentDistrict, currentWard);
            }
        })
        .catch(error => {
            console.error('Error loading districts:', error);
            districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            districtSelect.disabled = false;
        });
}

function loadEditWards(districtCode, currentWard = '') {
    const wardSelect = document.getElementById('edit-ward');

    wardSelect.innerHTML = '<option value="">Đang tải...</option>';
    wardSelect.disabled = true;

    if (!districtCode) {
        wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
        return;
    }

    fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
            wardSelect.disabled = false;
            wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
            
            data.wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.code;
                option.textContent = ward.name;
                if (ward.code === currentWard) {
                    option.selected = true;
                }
                wardSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading wards:', error);
            wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            wardSelect.disabled = false;
        });
}

// Event listeners for edit modal
document.addEventListener('DOMContentLoaded', function() {
    // Load provinces when edit modal opens
    document.getElementById('editUserModal').addEventListener('shown.bs.modal', function () {
        // Provinces will be loaded when openEditUserModal is called
    });

    // Province change event
    const editProvinceSelect = document.getElementById('edit-province');
    if (editProvinceSelect) {
        editProvinceSelect.addEventListener('change', function() {
            loadEditDistricts(this.value);
        });
    }

    // District change event
    const editDistrictSelect = document.getElementById('edit-district');
    if (editDistrictSelect) {
        editDistrictSelect.addEventListener('change', function() {
            loadEditWards(this.value);
        });
    }
});
</script>