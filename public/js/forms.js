function previewCreateMedicineImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('create-medicine-image-preview');
    const placeholder = document.getElementById('create-medicine-image-placeholder');
    
    if (file) {
        // Kiểm tra kích thước file anh tai len (2MB = 2 * 1024 * 1024 bytes)
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

// Image preview function for edit modal
function previewEditImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('edit-image-preview');
    const placeholder = document.getElementById('edit-image-placeholder');
    
    if (file) {
        // Kiểm tra kích thước file anh tai len (2MB = 2 * 1024 * 1024 bytes)
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
    
// Function to open edit modal and populate data
function openEditMedicineModal(medicineId) {
    // 1. Lấy modal element
    const modalEl = document.getElementById('editMedicineModal');
    if (!modalEl) {
        console.error('Edit Medicine Modal element not found!');
        return;
    }

    // 2. Fetch dữ liệu thuốc từ API
    fetch(`/admin/medicines/${medicineId}/detail`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const medicine = data.product;
                
                // 3. Map dữ liệu vào các field với CSS selector mới (medicine_edit_*)
                const fields = [
                    ['#medicine_edit_ma_hang', medicine.ma_hang || ''],
                    ['#medicine_edit_ma_vach', medicine.ma_vach || ''],
                    ['#medicine_edit_ten_thuoc', medicine.ten_thuoc || ''],
                    ['#medicine_edit_ten_viet_tat', medicine.ten_viet_tat || ''],
                    ['#medicine_edit_nhom_hang_id', medicine.nhom_hang_id || ''],
                    ['#medicine_edit_gia_von', medicine.gia_von || 0],
                    ['#medicine_edit_gia_ban', medicine.gia_ban || 0],
                    ['#medicine_edit_so_dang_ky', medicine.so_dang_ky || ''],
                    ['#medicine_edit_hoat_chat', medicine.hoat_chat || ''],
                    ['#medicine_edit_ham_luong', medicine.ham_luong || ''],
                    ['#medicine_edit_duong_dung_select', medicine.drugusage_id || ''],
                    ['#medicine_edit_manufacturer_select', medicine.manufacturer_id || ''],
                    ['#medicine_edit_nuoc_san_xuat', medicine.nuoc_san_xuat || ''],
                    ['#medicine_edit_quy_cach_dong_goi', medicine.quy_cach_dong_goi || ''],
                    ['#medicine_edit_ton_thap_nhat', medicine.ton_thap_nhat || 0],
                    ['#medicine_edit_ton_cao_nhat', medicine.ton_cao_nhat || 999999999],
                    ['#medicine_edit_position_select', medicine.position_id || ''],
                    ['#medicine_edit_trong_luong', medicine.trong_luong || 0],
                    ['#medicine_edit_don_vi_tinh_input', medicine.don_vi_tinh || ''],
                    ['#medicine_edit_mo_ta', medicine.mo_ta || '']
                ];
                
                // 4. Fill dữ liệu vào các input field
                fields.forEach(([selector, value]) => {
                    const element = modalEl.querySelector(selector);
                    if (element) {
                        element.value = value;
                        
                        // Format price fields immediately after setting value
                        if (selector.includes('gia_ban') || selector.includes('gia_von')) {
                            // Use setTimeout to ensure the value is set before formatting
                            setTimeout(() => {
                                if (window.formatPriceInput) {
                                    window.formatPriceInput(element);
                                }
                            }, 10);
                        }
                    }
                });
                
                // 5. Xử lý checkbox bán trực tiếp
                const banTrucTiep = modalEl.querySelector('#medicine_edit_ban_truc_tiep');
                if (banTrucTiep) {
                    banTrucTiep.checked = medicine.ban_truc_tiep == 1;
                }
                
                // 6. Set form action URL
                const form = modalEl.querySelector('#editMedicineForm');
                if (form) {
                    form.action = `/admin/medicines/${medicineId}`;
                }
                
                // 7. Xử lý hiển thị ảnh sản phẩm
                if (medicine.image) {
                    const preview = modalEl.querySelector('#edit-medicine-image-preview');
                    const placeholder = modalEl.querySelector('#edit-medicine-image-placeholder');
                    if (preview && placeholder) {
                        preview.src = `/storage/${medicine.image}`;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                }
                
                // 8. Hiển thị modal
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                alert('Không thể tải dữ liệu thuốc');
            }
        })
        .catch(error => {
            console.error('Error loading medicine data:', error);
            alert('Lỗi khi tải dữ liệu thuốc');
        });
}
// Inline form handlers for edit modal
function handleEditDrugRouteChange(select) {
    if (select.value === 'create_new') {
        document.getElementById('editDrugRouteInlineForm').style.display = 'block';
        document.getElementById('editNewDrugRouteName').focus();
        select.value = '';
    }
}

function createNewEditDrugRouteInline() {
    const name = document.getElementById('editNewDrugRouteName').value.trim();
    if (!name) {
        // Validation failed
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('/admin/products/drugroute', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.drug_route) {
            const select = document.getElementById('medicine_edit_duong_dung_select');
            const newOption = document.createElement('option');
            newOption.value = data.drug_route.id;
            newOption.textContent = data.drug_route.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.drug_route.id;
            
            cancelEditDrugRouteForm();
            showSuccessMessage('Tạo đường dùng thành công!');
        } else {
            // Error creating drug route
        }
    })
    .catch(error => {
        // Network or server error
    });
}

function cancelEditDrugRouteForm() {
    document.getElementById('editDrugRouteInlineForm').style.display = 'none';
    document.getElementById('editNewDrugRouteName').value = '';
}

function handleEditManufacturerChange(select) {
    if (select.value === 'create_new') {
        document.getElementById('editMedicineManufacturerInlineForm').style.display = 'block';
        document.getElementById('editNewMedicineManufacturerName').focus();
        select.value = '';
    }
}

function createNewEditManufacturerInline() {
    const name = document.getElementById('editNewMedicineManufacturerName').value.trim();
    if (!name) {
        // Validation failed
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('/admin/products/manufacturer', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.manufacturer) {
            const select = document.getElementById('medicine_edit_manufacturer_select');
            const newOption = document.createElement('option');
            newOption.value = data.manufacturer.id;
            newOption.textContent = data.manufacturer.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.manufacturer.id;
            
            cancelEditManufacturerForm();
            showSuccessMessage('Tạo hãng sản xuất thành công!');
        } else {
            // Error creating manufacturer
        }
    })
    .catch(error => {
        // Network or server error
    });
}

function cancelEditManufacturerForm() {
    document.getElementById('editMedicineManufacturerInlineForm').style.display = 'none';
    document.getElementById('editNewMedicineManufacturerName').value = '';
}

function handleEditPositionChange(select) {
    if (select.value === 'create_new') {
        document.getElementById('editMedicinePositionInlineForm').style.display = 'block';
        document.getElementById('editNewMedicinePositionName').focus();
        select.value = '';
    }
}

function createNewEditPositionInline() {
    const name = document.getElementById('editNewMedicinePositionName').value.trim();
    if (!name) {
        // Validation failed
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('/admin/products/position', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.position) {
            const select = document.getElementById('medicine_edit_position_select');
            const newOption = document.createElement('option');
            newOption.value = data.position.id;
            newOption.textContent = data.position.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.position.id;
            
            cancelEditPositionForm();
            showSuccessMessage('Tạo vị trí thành công!');
        } else {
            // Error creating position
        }
    })
    .catch(error => {
        // Network or server error
    });
}

function cancelEditPositionForm() {
    document.getElementById('editMedicinePositionInlineForm').style.display = 'none';
    document.getElementById('editNewMedicinePositionName').value = '';
}

// Unit modal functions for edit
function openEditUnitModal() {
    const modal = new bootstrap.Modal(document.getElementById('unitModal'));
    modal.show();
}

function openGoodsEditUnitModal() {
    const modal = new bootstrap.Modal(document.getElementById('unitModal'));
    modal.show();
}

// CREATE GOODS - Inline form handlers
function handleCreateManufacturerChange(select) {
    const inlineForm = document.getElementById('createGoodsManufacturerInlineForm');
    if (select.value === 'create_new') {
        inlineForm.style.display = 'block';
    } else {
        inlineForm.style.display = 'none';
    }
}

function handleCreatePositionChange(select) {
    const inlineForm = document.getElementById('createGoodsPositionInlineForm');
    if (select.value === 'create_new') {
        inlineForm.style.display = 'block';
    } else {
        inlineForm.style.display = 'none';
    }
}

function createNewCreateManufacturerInline() {
    const name = document.getElementById('createNewGoodsManufacturerName').value.trim();
    if (!name) {
        alert('Vui lòng nhập tên hãng sản xuất!');
        return;
    }

    fetch('/admin/products/manufacturer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name: name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Thêm option mới vào select
            const select = document.getElementById('manufacturer_id');
            const newOption = new Option(data.manufacturer.name, data.manufacturer.id);
            select.add(newOption);
            select.value = data.manufacturer.id;
            
            // Ẩn inline form
            document.getElementById('createGoodsManufacturerInlineForm').style.display = 'none';
            document.getElementById('createNewGoodsManufacturerName').value = '';
            
            showSuccessMessage('Tạo hãng sản xuất thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tạo hãng sản xuất!');
    });
}

function createNewCreatePositionInline() {
    const name = document.getElementById('createNewGoodsPositionName').value.trim();
    if (!name) {
        alert('Vui lòng nhập tên vị trí!');
        return;
    }

    fetch('/admin/products/position', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name: name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Thêm option mới vào select
            const select = document.getElementById('position_id');
            const newOption = new Option(data.position.name, data.position.id);
            select.add(newOption);
            select.value = data.position.id;
            
            // Ẩn inline form
            document.getElementById('createGoodsPositionInlineForm').style.display = 'none';
            document.getElementById('createNewGoodsPositionName').value = '';
            
            showSuccessMessage('Tạo vị trí thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tạo vị trí!');
    });
}

function cancelCreateManufacturerForm() {
    document.getElementById('createGoodsManufacturerInlineForm').style.display = 'none';
    document.getElementById('createNewGoodsManufacturerName').value = '';
    document.getElementById('manufacturer_id').value = '';
}

function cancelCreatePositionForm() {
    document.getElementById('createGoodsPositionInlineForm').style.display = 'none';
    document.getElementById('createNewGoodsPositionName').value = '';
    document.getElementById('position_id').value = '';
}

function openCreateUnitModal() {
    const modal = new bootstrap.Modal(document.getElementById('unitModal'));
    modal.show();
}

// Success message function
function showSuccessMessage(message) {
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
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Validate form fields
function validateForm(formId) {
    const form = document.getElementById(formId);
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Validate file upload
function validateFileUpload(input, maxSize = 2 * 1024 * 1024) {
    const file = input.files[0];
    
    if (file) {
        if (file.size > maxSize) {
            alert(`File quá lớn! Vui lòng chọn file nhỏ hơn ${maxSize / (1024 * 1024)}MB.`);
            input.value = '';
            return false;
        }
        
        if (!file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh!');
            input.value = '';
            return false;
        }
    }
    
    return true;
}
// Submit form with AJAX
function submitForm(formId, successCallback = null) {
    const form = document.getElementById(formId);
    
    if (!validateForm(formId)) {
        return false;
    }
    
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message || 'Thao tác thành công!');
            if (successCallback) {
                successCallback(data);
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi mạng!');
    });
}
// Reset form
function resetForm(formId) {
    const form = document.getElementById(formId);
    form.reset();
    
    // Remove validation classes
    const invalidFields = form.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => {
        field.classList.remove('is-invalid');
    });
}

// Clear form
function clearForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
}

// Focus first field
function focusFirstField(formId) {
    const form = document.getElementById(formId);
    const firstField = form.querySelector('input, select, textarea');
    if (firstField) {
        firstField.focus();
    }
}

// Generic inline form handler
function handleInlineFormChange(select, formId, inputId, type) {
    if (select.value === 'create_new') {
        document.getElementById(formId).style.display = 'block';
        document.getElementById(inputId).focus();
        select.value = '';
    }
}

// Generic create new inline function
function createNewInline(type) {
    const inputId = `editNew${type.charAt(0).toUpperCase() + type.slice(1)}Name`;
    const selectId = `edit_${type}_select`;
    const name = document.getElementById(inputId).value.trim();
    
    if (!name) {
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(`/admin/products/${type}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data[type]) {
            const select = document.getElementById(selectId);
            const newOption = document.createElement('option');
            newOption.value = data[type].id;
            newOption.textContent = data[type].name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data[type].id;
            
            cancelInlineForm(type);
            showSuccessMessage(`Tạo ${type} thành công!`);
        }
    })
    .catch(error => {
        console.error('Error creating:', error);
    });
}

// Generic cancel inline form function
function cancelInlineForm(type) {
    const formId = `edit${type.charAt(0).toUpperCase() + type.slice(1)}InlineForm`;
    const inputId = `editNew${type.charAt(0).toUpperCase() + type.slice(1)}Name`;
    document.getElementById(formId).style.display = 'none';
    document.getElementById(inputId).value = '';
}
// Image preview function for edit goods modal
function previewEditGoodsImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('edit-goods-image-preview');
    const placeholder = document.getElementById('edit-goods-image-placeholder');
    
    if (file) {
        // Check file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File quá lớn! Vui lòng chọn ảnh nhỏ hơn 2MB.');
            input.value = '';
            return;
        }
        
        // Check file type
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

// Function to open edit goods modal and populate data
/**
 * Mở modal chỉnh sửa hàng hóa và load dữ liệu
 * @param {number} goodsId - ID của hàng hóa cần chỉnh sửa
 */
function openEditGoodsModal(goodsId) {
    // 1. Lấy modal element
    const modalEl = document.getElementById('editGoodsModal');
    if (!modalEl) {
        console.error('Edit Goods Modal element not found!');
        return;
    }

    // 2. Fetch dữ liệu hàng hóa từ API
    fetch(`/admin/goods/${goodsId}/detail`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const goods = data.product;
                
                // 3. Map dữ liệu vào các field với ID mới (goods_edit_*)
                const fields = [
                    ['#goods_edit_ma_hang', goods.ma_hang || ''],
                    ['#goods_edit_ma_vach', goods.ma_vach || ''],
                    ['#goods_edit_ten_hang_hoa', goods.ten_hang_hoa || ''],
                    ['#goods_edit_nhom_hang_id', goods.nhom_hang_id || ''],
                    ['#goods_edit_manufacturer_id', goods.manufacturer_id || ''],
                    ['#goods_edit_gia_von', goods.gia_von || 0],
                    ['#goods_edit_gia_ban', goods.gia_ban || 0],
                    ['#goods_edit_quy_cach_dong_goi', goods.quy_cach_dong_goi || ''],
                    ['#goods_edit_nuoc_san_xuat', goods.nuoc_san_xuat || ''],
                    ['#goods_edit_ton_thap_nhat', goods.ton_thap_nhat || 0],
                    ['#goods_edit_ton_cao_nhat', goods.ton_cao_nhat || 999999999],
                    ['#goods_edit_position_id', goods.position_id || ''],
                    ['#goods_edit_trong_luong', goods.trong_luong || 0],
                    ['#goods_edit_don_vi_tinh_input', goods.don_vi_tinh || ''],
                    ['#goods_edit_mo_ta', goods.mo_ta || '']
                ];
                
                // 4. Fill dữ liệu vào các input field
                fields.forEach(([selector, value]) => {
                    const element = modalEl.querySelector(selector);
                    if (element) {
                        element.value = value;
                        
                        // Format price fields immediately after setting value
                        if (selector.includes('gia_ban') || selector.includes('gia_von')) {
                            // Use setTimeout to ensure the value is set before formatting
                            setTimeout(() => {
                                if (window.formatPriceInput) {
                                    window.formatPriceInput(element);
                                }
                            }, 10);
                        }
                    }
                });
                
                // 5. Xử lý checkbox bán trực tiếp
                const banTrucTiep = modalEl.querySelector('#goods_edit_ban_truc_tiep');
                if (banTrucTiep) {
                    banTrucTiep.checked = goods.ban_truc_tiep == 1;
                }
                
                // 6. Set form action URL
                const form = modalEl.querySelector('#editGoodsForm');
                if (form) {
                    form.action = `/admin/goods/${goodsId}`;
                }
                
                // 7. Xử lý hiển thị ảnh sản phẩm
                if (goods.image) {
                    const preview = modalEl.querySelector('#edit-goods-image-preview');
                    const placeholder = modalEl.querySelector('#edit-goods-image-placeholder');
                    if (preview && placeholder) {
                        preview.src = `/storage/${goods.image}`;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                }
                
                // 8. Hiển thị modal
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                alert('Không thể tải dữ liệu hàng hóa');
            }
        })
        .catch(error => {
            console.error('Error loading goods data:', error);
            alert('Lỗi khi tải dữ liệu hàng hóa');
        });
}

// Handle manufacturer change for goods edit modal
function handleEditManufacturerChange(select) {
    if (select.value === 'create_new') {
        document.getElementById('editGoodsManufacturerInlineForm').style.display = 'block';
        document.getElementById('editNewGoodsManufacturerName').focus();
        select.value = '';
    }
}

// Handle position change for goods edit modal
function handleEditPositionChange(select) {
    if (select.value === 'create_new') {
        document.getElementById('editGoodsPositionInlineForm').style.display = 'block';
        document.getElementById('editNewGoodsPositionName').focus();
        select.value = '';
    }
}

// Create new manufacturer inline for goods edit modal
function createNewEditManufacturerInline() {
    const name = document.getElementById('editNewGoodsManufacturerName').value.trim();
    if (!name) {
        alert('Vui lòng nhập tên hãng sản xuất!');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('/admin/products/manufacturer', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.manufacturer) {
            const select = document.getElementById('goods_edit_manufacturer_id');
            const newOption = document.createElement('option');
            newOption.value = data.manufacturer.id;
            newOption.textContent = data.manufacturer.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.manufacturer.id;
            
            cancelEditManufacturerForm();
            showSuccessMessage('Tạo hãng sản xuất thành công!');
        } else {
            alert('Có lỗi xảy ra khi tạo hãng sản xuất!');
        }
    })
    .catch(error => {
        console.error('Error creating manufacturer:', error);
        alert('Đã xảy ra lỗi mạng!');
    });
}

// Create new position inline for goods edit modal
function createNewEditPositionInline() {
    const name = document.getElementById('editNewGoodsPositionName').value.trim();
    if (!name) {
        alert('Vui lòng nhập tên vị trí!');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('/admin/products/position', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.position) {
            const select = document.getElementById('goods_edit_position_id');
            const newOption = document.createElement('option');
            newOption.value = data.position.id;
            newOption.textContent = data.position.name;
            const createNewOption = select.querySelector('option[value="create_new"]');
            select.insertBefore(newOption, createNewOption);
            select.value = data.position.id;
            
            cancelEditPositionForm();
            showSuccessMessage('Tạo vị trí thành công!');
        } else {
            alert('Có lỗi xảy ra khi tạo vị trí!');
        }
    })
    .catch(error => {
        console.error('Error creating position:', error);
        alert('Đã xảy ra lỗi mạng!');
    });
}

// Cancel manufacturer form for goods edit modal
function cancelEditManufacturerForm() {
    document.getElementById('editGoodsManufacturerInlineForm').style.display = 'none';
    document.getElementById('editNewGoodsManufacturerName').value = '';
}

// Cancel position form for goods edit modal
function cancelEditPositionForm() {
    document.getElementById('editGoodsPositionInlineForm').style.display = 'none';
    document.getElementById('editNewGoodsPositionName').value = '';
}

// Unit modal function for goods edit modal
function openEditUnitModal() {
    const modal = new bootstrap.Modal(document.getElementById('unitModal'));
    modal.show();
}

// Function to open edit service modal and populate data
function openEditServiceModal(serviceId) {
    // 1. Lấy modal element
    const modalEl = document.getElementById('editServiceModal');
    if (!modalEl) {
        console.error('Edit Service Modal element not found!');
        return;
    }

    // 2. Fetch dữ liệu dịch vụ từ API
    fetch(`/admin/services/${serviceId}/detail`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const service = data.product;
                
                // 3. Map dữ liệu vào các field với CSS selector mới (service_edit_*)
                const fields = [
                    ['#service_edit_ma_hang', service.ma_hang || ''],
                    ['#service_edit_ten_dich_vu', service.ten_dich_vu || ''],
                    ['#service_edit_nhom_dich_vu_id', service.nhom_hang_id || ''],
                    ['#service_edit_gia_ban', service.gia_dich_vu || 0],
                    ['#service_edit_hinh_thuc', service.hinh_thuc || ''],
                    ['#service_edit_thoi_gian_thuc_hien', service.thoi_gian_thuc_hien || ''],
                    ['#service_edit_trang_thai', service.trang_thai || ''],
                    ['#service_edit_mo_ta', service.mo_ta || ''],
                    ['#service_edit_ghi_chu', service.ghi_chu || '']
                ];
                
                // 4. Fill dữ liệu vào các input field
                fields.forEach(([selector, value]) => {
                    const element = modalEl.querySelector(selector);
                    if (element) {
                        element.value = value;
                        
                        // Format price fields immediately after setting value
                        if (selector.includes('gia_ban')) {
                            // Use setTimeout to ensure the value is set before formatting
                            setTimeout(() => {
                                if (window.formatPriceInput) {
                                    window.formatPriceInput(element);
                                }
                            }, 10);
                        }
                    }
                });
                
                // 5. Set form action URL
                const form = modalEl.querySelector('#editServiceForm');
                if (form) {
                    form.action = `/admin/services/${serviceId}`;
                }
                
                // 6. Xử lý hiển thị ảnh dịch vụ
                if (service.image) {
                    const preview = modalEl.querySelector('#edit-service-image-preview');
                    const placeholder = modalEl.querySelector('#edit-service-image-placeholder');
                    if (preview && placeholder) {
                        preview.src = `/storage/${service.image}`;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                }
                
                // 7. Hiển thị modal
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                alert('Không thể tải dữ liệu dịch vụ');
            }
        })
        .catch(error => {
            console.error('Error loading service data:', error);
            alert('Lỗi khi tải dữ liệu dịch vụ');
        });
}


/**
 * Preview selected image for create service
 */
function previewCreateServiceImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('createServiceImagePreview');
    const placeholder = document.getElementById('createServiceImagePlaceholder');
    
    if (file) {
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('File quá lớn! Vui lòng chọn ảnh nhỏ hơn 2MB.');
            input.value = '';
            return;
        }
        
        // Check file type
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

/**
 * Preview selected image for edit service
 */
function previewEditServiceImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('editServiceImagePreview');
    const placeholder = document.getElementById('editServiceImagePlaceholder');
    
    if (file) {
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('File quá lớn! Vui lòng chọn ảnh nhỏ hơn 2MB.');
            input.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh!');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
    }
}

/**
 * Auto-generate service code based on service name
 */
function generateServiceCode() {
    const serviceName = document.getElementById('ten_dich_vu');
    const serviceCode = document.getElementById('ma_dich_vu');
    
    if (serviceName && serviceCode && serviceName.value && !serviceCode.value) {
        // Simple code generation: take first letters and add random numbers
        const words = serviceName.value.trim().split(' ');
        let code = 'DV';
        
        words.forEach(word => {
            if (word.length > 0) {
                code += word.charAt(0).toUpperCase();
            }
        });
        
        // Add random 3-digit number
        code += String(Math.floor(Math.random() * 900) + 100);
        
        serviceCode.value = code;
    }
}

/**
 * Calculate estimated completion time based on service type
 */
function updateEstimatedTime() {
    const serviceType = document.getElementById('hinh_thuc');
    const timeInput = document.getElementById('thoi_gian_thuc_hien');
    
    if (serviceType && timeInput) {
        const type = serviceType.value;
        
        // Suggest default times based on service type
        if (type === 'tai_nha_thuoc' && !timeInput.value) {
            timeInput.placeholder = 'VD: 15-30 phút';
        } else if (type === 'tai_nha_khach' && !timeInput.value) {
            timeInput.placeholder = 'VD: 45-60 phút';
        }
    }
}

/**
 * Validate service form before submission
 */
function validateServiceForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    let firstInvalidField = null;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            if (!firstInvalidField) {
                firstInvalidField = field;
            }
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Validate price
    const priceField = form.querySelector('[name="gia_ban"]');
    if (priceField && parseFloat(priceField.value) < 0) {
        priceField.classList.add('is-invalid');
        isValid = false;
        if (!firstInvalidField) {
            firstInvalidField = priceField;
        }
    }
    
    // Focus on first invalid field
    if (firstInvalidField) {
        firstInvalidField.focus();
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    return isValid;
}

/**
 * Reset service form
 */
function resetServiceForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        
        // Reset image preview
        const preview = form.querySelector('img[id*="Preview"]');
        const placeholder = form.querySelector('div[id*="Placeholder"]');
        
        if (preview) preview.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
        
        // Remove validation classes
        form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
    }
}

/**
 * Format price input with thousand separators
 */
function formatServicePrice(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('vi-VN');
        input.value = value;
    }
}

/**
 * Initialize service form event listeners
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate service code when service name changes
    const serviceNameInput = document.getElementById('ten_dich_vu');
    if (serviceNameInput) {
        serviceNameInput.addEventListener('blur', generateServiceCode);
    }
    
    // Update estimated time when service type changes
    const serviceTypeSelect = document.getElementById('hinh_thuc');
    if (serviceTypeSelect) {
        serviceTypeSelect.addEventListener('change', updateEstimatedTime);
    }
    
    // Format price inputs - Unified price formatting for all price inputs
    const formatPrice = function(input) {
        // Lấy giá trị hiện tại
        let value = input.value;
        
        // Xử lý các trường hợp đặc biệt từ database
        if (value && typeof value === 'string') {
            // Loại bỏ .00 ở cuối nếu có (ví dụ: "230000.00" -> "230000")
            value = value.replace(/\.00$/, '');
            // Loại bỏ tất cả ký tự không phải số
            value = value.replace(/\D/g, '');
        } else if (typeof value === 'number') {
            // Nếu là số, chuyển thành string và loại bỏ .00
            value = value.toString().replace(/\.00$/, '');
        }
        
        // Format với dấu phân cách hàng nghìn
        if (value && value !== '0') {
            input.value = new Intl.NumberFormat('vi-VN').format(parseInt(value));
        } else if (value === '0') {
            input.value = '0';
        }
    };
    
    // Global function to format price - can be called from anywhere
    window.formatPriceInput = formatPrice;
    
    // Apply to all price inputs (both name selectors and class selectors)
    const priceInputs = document.querySelectorAll('input[name="gia_ban"], input[name="gia_von"], .price-input');
    priceInputs.forEach(input => {
        // Format existing value on load (edit forms)
        if (input.value && /\d/.test(input.value)) {
            formatPrice(input);
        }

        // Format while typing
        input.addEventListener('input', function() {
            formatPrice(this);
        });
        
        // Select all text when focus for easy editing
        input.addEventListener('focus', function() {
            this.select();
        });
        
        // Format when blur (finish typing)
        input.addEventListener('blur', function() {
            if (this.value) {
                formatPrice(this);
            }
        });
    });

    // Before submitting any form, strip separators to store plain numbers in DB
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const scopedPriceInputs = form.querySelectorAll('input[name="gia_ban"], input[name="gia_von"], .price-input');
            scopedPriceInputs.forEach(input => {
                input.value = (input.value || '').replace(/\D/g, '');
            });
        });
    });
});

//# sourceMappingURL=forms.js.map