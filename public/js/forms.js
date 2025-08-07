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
    fetch(`/admin/medicines/${medicineId}/detail`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const medicine = data.product;
                
                // Populate form fields
                const fields = {
                    'edit_ma_hang': medicine.ma_hang || '',
                    'edit_ma_vach': medicine.ma_vach || '',
                    'edit_ten_thuoc': medicine.ten_thuoc || '',
                    'edit_ten_viet_tat': medicine.ten_viet_tat || '',
                    'edit_nhom_hang_id': medicine.nhom_hang_id || '',
                    'edit_gia_von': medicine.gia_von || 0,
                    'edit_gia_ban': medicine.gia_ban || 0,
                    'edit_so_dang_ky': medicine.so_dang_ky || '',
                    'edit_hoat_chat': medicine.hoat_chat || '',
                    'edit_ham_luong': medicine.ham_luong || '',
                    'edit_duong_dung_select': medicine.drugusage_id || '',
                    'edit_manufacturer_select': medicine.manufacturer_id || '',
                    'edit_nuoc_san_xuat': medicine.nuoc_san_xuat || '',
                    'edit_quy_cach_dong_goi': medicine.quy_cach_dong_goi || '',
                    'edit_ton_thap_nhat': medicine.ton_thap_nhat || 0,
                    'edit_ton_cao_nhat': medicine.ton_cao_nhat || 999999999,
                    'edit_position_select': medicine.position_id || '',
                    'edit_trong_luong': medicine.trong_luong || 0,
                    'edit_don_vi_tinh_input': medicine.don_vi_tinh || '',
                    'edit_mo_ta': medicine.mo_ta || ''
                };
                
                // Set all field values
                Object.keys(fields).forEach(fieldId => {
                    const element = document.getElementById(fieldId);
                    if (element) {
                        element.value = fields[fieldId];
                    }
                });
                
                // Set checkbox
                const banTrucTiep = document.getElementById('edit_ban_truc_tiep');
                if (banTrucTiep) {
                    banTrucTiep.checked = medicine.ban_truc_tiep == 1;
                }
                
                // Set form action
                const form = document.getElementById('editMedicineForm');
                if (form) {
                    form.action = `/admin/medicines/${medicineId}`;
                }
                
                // Show current image if exists
                if (medicine.image) {
                    const preview = document.getElementById('edit-image-preview');
                    const placeholder = document.getElementById('edit-image-placeholder');
                    if (preview && placeholder) {
                        preview.src = `/storage/${medicine.image}`;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                }
                
                // Open modal
                const modal = new bootstrap.Modal(document.getElementById('editMedicineModal'));
                modal.show();
            } else {
                // Failed to load medicine data
            }
        })
        .catch(error => {
            // Error loading medicine data
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
            const select = document.getElementById('edit_duong_dung_select');
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
        document.getElementById('editManufacturerInlineForm').style.display = 'block';
        document.getElementById('editNewManufacturerName').focus();
        select.value = '';
    }
}

function createNewEditManufacturerInline() {
    const name = document.getElementById('editNewManufacturerName').value.trim();
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
            const select = document.getElementById('edit_manufacturer_select');
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
    document.getElementById('editManufacturerInlineForm').style.display = 'none';
    document.getElementById('editNewManufacturerName').value = '';
}

function handleEditPositionChange(select) {
    if (select.value === 'create_new') {
        document.getElementById('editPositionInlineForm').style.display = 'block';
        document.getElementById('editNewPositionName').focus();
        select.value = '';
    }
}

function createNewEditPositionInline() {
    const name = document.getElementById('editNewPositionName').value.trim();
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
            const select = document.getElementById('edit_position_select');
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
    document.getElementById('editPositionInlineForm').style.display = 'none';
    document.getElementById('editNewPositionName').value = '';
}

// Unit modal functions for edit
function openEditUnitModal() {
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

// ===== FORM SUBMISSION FUNCTIONS =====

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

// ===== FORM UTILITY FUNCTIONS =====

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

// ===== INLINE FORM FUNCTIONS =====

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
