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
        $(document).ready(function() {
            const provinceSelect = document.getElementById('khu_vuc');
            const wardSelect = document.getElementById('phuong_xa');
            
            // Load provinces when modal opens
            const modal = document.getElementById('createSupplierModal');
            modal.addEventListener('shown.bs.modal', function() {
                if (provinceSelect.options.length <= 1) { // Chỉ load nếu chưa có data
                    loadProvinces();
                }
            });
            
            // Sử dụng shared ProvinceService
            async function loadProvinces() {
                try 
                {
                    await window.provinceService.populateProvinceSelect(provinceSelect, '-- Chọn tỉnh/thành --');
                } 
                catch (error) 
                {
                    console.error('Lỗi load tỉnh/thành:', error);
                    alert('Không thể tải danh sách tỉnh/thành. Vui lòng thử lại!\nLỗi: ' + error.message);
                }
            }
            
            // Sử dụng shared ProvinceService
            async function loadWards(provinceCode) {
                try {
                    console.log('Đang tải phường/xã cho tỉnh:', provinceCode);
                    
                    wardSelect.disabled = true;
                    wardSelect.innerHTML = '<option value="">Đang tải...</option>';
                    
                    await window.provinceService.populateWardSelect(wardSelect, provinceCode, '-- Chọn phường/xã --');
                    
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
                const provinceCode = this.value; // là code sau khi chỉnh ở trên
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
                    // Gửi tên tỉnh/thành
                    let hiddenProvinceInput = document.getElementById('hidden_province_name');
                    if (!hiddenProvinceInput) {
                        hiddenProvinceInput = document.createElement('input');
                        hiddenProvinceInput.type = 'hidden';
                        hiddenProvinceInput.id = 'hidden_province_name';
                        hiddenProvinceInput.name = 'khu_vuc';
                        form.appendChild(hiddenProvinceInput);
                    }
                    hiddenProvinceInput.value = selectedProvince.dataset.name;
                }
                
                if (selectedWard && selectedWard.dataset.name) {
                    // Ghi đè giá trị input chính với TEXT  
                    let hiddenWardInput = document.getElementById('hidden_ward_name');
                    if (!hiddenWardInput) {
                        hiddenWardInput = document.createElement('input');
                        hiddenWardInput.type = 'hidden';
                        hiddenWardInput.id = 'hidden_ward_name';
                        hiddenWardInput.name = 'phuong_xa';
                        form.appendChild(hiddenWardInput);
                    }
                    hiddenWardInput.value = selectedWard.dataset.name;
                }
            });
        });



        // Handle submit form edit
        $('#editSupplierForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(this);
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Cập nhật thành công!');
                        $('#editSupplierModal').modal('hide');
                        location.reload(); // Reload trang
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra!');
                }
            });
        });
    </script>


    {{-- edit supplier --}}
    <script>      
        function editSupplier(supplierId) {
            console.log('editSupplier called with ID:', supplierId);
            
            // Load dữ liệu supplier
            $.get(`/admin/suppliers/${supplierId}/edit`)
                .done(function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        const supplier = response.supplier;
                        
                        // Điền dữ liệu vào form (đơn giản)
                        $('#edit_ten_nha_cung_cap').val(supplier.ten_nha_cung_cap);
                        $('#edit_ma_nha_cung_cap').val(supplier.ma_nha_cung_cap);
                        $('#edit_dien_thoai').val(supplier.dien_thoai);
                        $('#edit_email').val(supplier.email);
                        $('#edit_dia_chi').val(supplier.dia_chi);
                        $('#edit_khu_vuc').val(supplier.khu_vuc);
                        $('#edit_phuong_xa').val(supplier.phuong_xa);
                        $('#edit_ten_cong_ty').val(supplier.ten_cong_ty);
                        $('#edit_ma_so_thue').val(supplier.ma_so_thue);
                        $('#edit_ghi_chu').val(supplier.ghi_chu);
                        $('#edit_nhom_nha_cung_cap_id').val(supplier.nhom_nha_cung_cap_id);
                        
                        // Set action cho form
                        $('#editSupplierForm').attr('action', `/admin/suppliers/${supplierId}`);
                        
                        // Hiển thị modal
                        $('#editSupplierModal').modal('show');
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('AJAX Error:', xhr, status, error);
                    alert('Lỗi tải dữ liệu!');
                });
        }
    </script>