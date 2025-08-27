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
        
        // Load provinces when modal opens
        const modal = document.getElementById('createSupplierModal');
        modal.addEventListener('shown.bs.modal', function() {
            if (provinceSelect.options.length <= 1) { // Chỉ load nếu chưa có data
                loadProvinces();
            }
        });
        
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
            } catch (error) {
                console.error('Lỗi load tỉnh/thành:', error);
                provinceSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                alert('Không thể tải danh sách tỉnh/thành. Vui lòng thử lại!\nLỗi: ' + error.message);
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
    });
</script>
