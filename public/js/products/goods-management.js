// Edit Medicine JavaScript

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

// ========================================
// GOODS MANAGEMENT FUNCTIONS
// ========================================

// Toggle hiển thị thông tin chi tiết hàng hóa
window.toggleGoodsDetail = function(goodsId, element) {
    const detailRow = document.getElementById(`detail-row-goods-${goodsId}`);
    if (!detailRow) return;
    const isVisible = detailRow.style.display !== 'none';
    // Đóng tất cả các detail rows khác
    document.querySelectorAll('.detail-row').forEach(row => {
        row.style.display = 'none';
    });
    // Xóa highlight từ tất cả các hàng
    document.querySelectorAll('.product-table tbody tr').forEach(row => {
        row.classList.remove('selected-row');
    });
    if (!isVisible) {
        // Mở detail row
        detailRow.style.display = 'table-row';
        // Highlight hàng được chọn
        const selectedRow = element.closest('tr');
        if (selectedRow) {
            selectedRow.classList.add('selected-row');
        }
        // Scroll đến detail row
        detailRow.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }
}

// Delete goods confirmation
window.showDeleteGoodsConfirmation = function(goodsId, goodsCode, goodsName) {
    // Set modal content
    document.getElementById('deleteMedicineId').value = goodsId;
    document.getElementById('deleteMedicineCode').textContent = goodsCode;
    document.getElementById('deleteMedicineName').textContent = goodsName;
    
    // Đánh dấu đây là hàng hóa để confirmDelete biết
    window.isDeletingGoods = true;
    
    // Show modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteModal.show();
}

// Confirm delete function for goods
window.confirmDelete = function() {
    const goodsId = document.getElementById('deleteMedicineId').value;
    const form = document.getElementById('deleteMedicineForm');
    
    // Kiểm tra xem đây là thuốc hay hàng hóa dựa vào biến global
    if (window.isDeletingGoods === true) {
        // Đây là hàng hóa
        form.action = `/admin/goods/${goodsId}`;
        console.log('Deleting goods with ID:', goodsId);
    } else {
        // Đây là thuốc
        form.action = `/admin/medicines/${goodsId}`;
        console.log('Deleting medicine with ID:', goodsId);
    }
    form.submit();
}

// Open edit goods modal
window.openEditGoodsModal = function(goodsId) {
    // Gọi API để lấy thông tin hàng hóa
    fetch(`/admin/goods/${goodsId}/detail`)
        .then(response => {
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const goods = data.product;
                // Populate form fields
                const fields = {
                    'edit_ma_hang': goods.ma_hang || '',
                    'edit_ma_vach': goods.ma_vach || '',
                    'edit_ten_hang_hoa': goods.ten_hang_hoa || '',
                    'edit_nhom_hang_id': goods.nhom_hang_id || '',
                    'edit_gia_von': goods.gia_von || 0,
                    'edit_gia_ban': goods.gia_ban || 0,
                    'edit_quy_cach_dong_goi': goods.quy_cach_dong_goi || '',
                    'edit_manufacturer_select': goods.manufacturer_id || '',
                    'edit_nuoc_san_xuat': goods.nuoc_san_xuat || '',
                    'edit_ton_kho': goods.ton_kho || 0,
                    'edit_ton_thap_nhat': goods.ton_thap_nhat || 0,
                    'edit_ton_cao_nhat': goods.ton_cao_nhat || 999999999,
                    'edit_position_select': goods.position_id || '',
                    'edit_trong_luong': goods.trong_luong || 0,
                    'edit_don_vi_tinh_input': goods.don_vi_tinh || '',
                    'edit_mo_ta': goods.mo_ta || ''
                };
                // Set form values
                Object.keys(fields).forEach(fieldId => {
                    const element = document.getElementById(fieldId);
                    if (element) {
                        element.value = fields[fieldId];
                    }
                });
                // Set checkbox
                const banTrucTiep = document.getElementById('edit_ban_truc_tiep');
                if (banTrucTiep) {
                    banTrucTiep.checked = goods.ban_truc_tiep == 1;
                }
                // Set form action
                const form = document.getElementById('editGoodsForm');
                if (form) {
                    form.action = `/admin/goods/${goodsId}`;
                }
                // Show current image if exists
                if (goods.image) {
                    const preview = document.getElementById('edit-image-preview');
                    const placeholder = document.getElementById('edit-image-placeholder');
                    if (preview && placeholder) {
                        preview.src = `/storage/${goods.image}`;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                }
                // Open modal
                const modalElement = document.getElementById('editGoodsModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    alert('Không tìm thấy modal chỉnh sửa!');
                }
            } else {
                alert('Không thể tải thông tin hàng hóa!');
            }
        })
        .catch(error => {
            console.error('Error loading goods data:', error);
            alert('Đã xảy ra lỗi khi tải thông tin hàng hóa!');
        });
}

// Filter products function
window.filterProducts = function() {
    const categoryId = document.querySelector('select[name="category_id"]').value;
    const manufacturerId = document.querySelector('select[name="manufacturer_id"]').value;
    const positionId = document.querySelector('select[name="position_id"]').value;
    const productType = document.querySelector('select[name="product_type"]').value;
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    
    // Lấy tất cả các hàng sản phẩm
    const productRows = document.querySelectorAll('.product-row');
    
    productRows.forEach(row => {
        let showRow = true;
        
        // Lọc theo loại sản phẩm
        if (productType) {
            const isMedicine = row.classList.contains('medicine-row');
            const isGoods = row.classList.contains('goods-row');
            
            if (productType === 'medicine' && !isMedicine) {
                showRow = false;
            } else if (productType === 'goods' && !isGoods) {
                showRow = false;
            }
        }
        
        // Lọc theo vị trí (nếu có data-position-id)
        if (positionId && showRow) {
            const rowPositionId = row.getAttribute('data-position-id');
            if (rowPositionId && rowPositionId !== positionId) {
                showRow = false;
            }
        }
        
        // Lọc theo nhà cung cấp (nếu có data-manufacturer-id)
        if (manufacturerId && showRow) {
            const rowManufacturerId = row.getAttribute('data-manufacturer-id');
            if (rowManufacturerId && rowManufacturerId !== manufacturerId) {
                showRow = false;
            }
        }
        
        // Lọc theo nhóm hàng (nếu có data-category-id)
        if (categoryId && showRow) {
            const rowCategoryId = row.getAttribute('data-category-id');
            if (rowCategoryId && rowCategoryId !== categoryId) {
                showRow = false;
            }
        }
        
        // Lọc theo từ khóa tìm kiếm
        if (searchTerm && showRow) {
            const productName = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const productCode = row.querySelector('.product-code')?.textContent.toLowerCase() || '';
            const manufacturer = row.querySelector('[data-manufacturer-name]')?.getAttribute('data-manufacturer-name')?.toLowerCase() || '';
            
            const isMatch = productName.includes(searchTerm) || 
                           productCode.includes(searchTerm) || 
                           manufacturer.includes(searchTerm);
            
            if (!isMatch) {
                showRow = false;
            }
        }
        
        // Hiển thị/ẩn hàng
        if (showRow) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Cập nhật số lượng sản phẩm hiển thị
    updateProductCount();
}

// Update product count
window.updateProductCount = function() {
    const visibleRows = document.querySelectorAll('.product-row:not([style*="display: none"])');
    const totalProducts = document.querySelectorAll('.product-row').length;
    
    // Cập nhật thông tin hiển thị
    const summaryElement = document.querySelector('.summary-section small');
    if (summaryElement) {
        summaryElement.innerHTML = `Tổng cộng: <strong>${totalProducts}</strong> sản phẩm | Hiển thị: <strong>${visibleRows.length}</strong> sản phẩm`;
    }
}



// Initialize filters when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Cập nhật số lượng sản phẩm ban đầu
    updateProductCount();
}); 

// ========================================
// SEARCH FUNCTIONALITY
// ========================================

// Search products function
window.searchProducts = function() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const productRows = document.querySelectorAll('.product-row');
    
    if (searchTerm === '') {
        // Nếu không có từ khóa tìm kiếm, hiển thị tất cả
        productRows.forEach(row => {
            row.style.display = '';
        });
        updateProductCount();
        return;
    }
    
    productRows.forEach(row => {
        const productName = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
        const productCode = row.querySelector('.product-code')?.textContent.toLowerCase() || '';
        const manufacturer = row.querySelector('[data-manufacturer-name]')?.getAttribute('data-manufacturer-name')?.toLowerCase() || '';
        
        // Tìm kiếm trong tên, mã và nhà cung cấp
        const isMatch = productName.includes(searchTerm) || 
        productCode.includes(searchTerm) || 
        manufacturer.includes(searchTerm);
        
        if (isMatch) {
            row.style.display = '';
            // Highlight từ khóa tìm kiếm
            highlightSearchTerm(row, searchTerm);
        } else {
            row.style.display = 'none';
        }
    });
    
    updateProductCount();
}

// Highlight từ khóa tìm kiếm
function highlightSearchTerm(row, searchTerm) {
    const productNameElement = row.querySelector('.product-name');
    const productCodeElement = row.querySelector('.product-code');
    
    if (productNameElement) {
        const originalText = productNameElement.textContent;
        const highlightedText = originalText.replace(
            new RegExp(searchTerm, 'gi'),
            match => `<mark style="background-color: #ffeb3b; padding: 1px 2px; border-radius: 2px;">${match}</mark>`
        );
        productNameElement.innerHTML = highlightedText;
    }
    
    if (productCodeElement) {
        const originalText = productCodeElement.textContent;
        const highlightedText = originalText.replace(
            new RegExp(searchTerm, 'gi'),
            match => `<mark style="background-color: #ffeb3b; padding: 1px 2px; border-radius: 2px;">${match}</mark>`
        );
        productCodeElement.innerHTML = highlightedText;
    }
}

// Clear search function
window.clearSearch = function() {
    document.getElementById('searchInput').value = '';
    
    // Xóa highlight
    const productRows = document.querySelectorAll('.product-row');
    productRows.forEach(row => {
        row.style.display = '';
        const productNameElement = row.querySelector('.product-name');
        const productCodeElement = row.querySelector('.product-code');
        
        if (productNameElement) {
            productNameElement.innerHTML = productNameElement.textContent;
        }
        if (productCodeElement) {
            productCodeElement.innerHTML = productCodeElement.textContent;
        }
    });
    
    updateProductCount();
}

// Debounce function để tối ưu performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Áp dụng debounce cho search function
window.searchProducts = debounce(window.searchProducts, 300); 