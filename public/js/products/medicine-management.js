document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal functionality
    const modal = document.getElementById('createCategoryModal');
    if (modal) {
        // Modal found
    }
    
    // Check if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        // Bootstrap loaded
    }
    
    // Test modal trigger
    const createLink = document.querySelector('[data-bs-target="#createCategoryModal"]');
    if (createLink) {
        createLink.addEventListener('click', function(e) {
            // Create link clicked
        });
    }
    
    // Form submission handling
    const categoryForm = document.querySelector('#createCategoryModal form');
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            // Form submitted
            const formData = new FormData(this);
            // Process form data
        });
    }
    
    // Test manual modal opening
    window.testModal = function() {
        const modal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
        modal.show();
    };
});

// Toggle hiển thị thông tin chi tiết sản phẩm
window.toggleProductDetail = function(productId, element) {
    const detailRow = document.getElementById(`detail-row-${productId}`);
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

// Format tiền tệ
function formatCurrency(amount) {
    if (!amount) return '0 VNĐ';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Placeholder functions for new actions
window.deleteMedicine = function(medicineId) {
    if (confirm('Bạn có chắc chắn muốn xóa thuốc này?')) {
        // Tạo form để gửi request DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/medicines/${medicineId}`;
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Function để mở modal chỉnh sửa sản phẩm
window.openEditMedicineModal = function(medicineId) {
    // Gọi API để lấy thông tin sản phẩm
    fetch(`/admin/medicines/${medicineId}/detail`)
        .then(response => {
            return response.json();
        })
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
                const modalElement = document.getElementById('editMedicineModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    alert('Không tìm thấy modal chỉnh sửa!');
                }
            } else {
                alert('Không thể tải thông tin thuốc!');
            }
        })
        .catch(error => {
            console.error('Error loading medicine data:', error);
            alert('Đã xảy ra lỗi khi tải thông tin thuốc!');
        });
}

// Delete confirmation modal functions
window.showDeleteConfirmation = function(medicineId, medicineCode, medicineName) {
    // Set modal content
    document.getElementById('deleteMedicineId').value = medicineId;
    document.getElementById('deleteMedicineCode').textContent = medicineCode;
    document.getElementById('deleteMedicineName').textContent = medicineName;
    
    // Đánh dấu đây là thuốc để confirmDelete biết
    window.isDeletingGoods = false;
    
    // Show modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteModal.show();
}

// Confirm delete function for medicines
window.confirmDelete = function() {
    const medicineId = document.getElementById('deleteMedicineId').value;
    const form = document.getElementById('deleteMedicineForm');
    
    // Kiểm tra xem đây là thuốc hay hàng hóa dựa vào biến global
    if (window.isDeletingGoods === true) {
        // Đây là hàng hóa
        form.action = `/admin/goods/${medicineId}`;
        console.log('Deleting goods with ID:', medicineId);
    } else {
        // Đây là thuốc
        form.action = `/admin/medicines/${medicineId}`;
        console.log('Deleting medicine with ID:', medicineId);
    }
    form.submit();
}



// Open unit modal
window.openUnitModal = function(medicineId) {
    const modalElement = document.getElementById('unitModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        alert('Modal đơn vị tính không tìm thấy!');
    }
} 

//tim kiem san pham
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
        }   
        else 
        {
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
        const originalText = productCodeElement.textContent; //original text lay noi dung text goc
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