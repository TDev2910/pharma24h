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

// Ẩn tất cả detail rows
function hideAllDetailRows() {
    document.querySelectorAll('.detail-row').forEach(row => {
        row.style.display = 'none';
    });
    
    // Xóa highlight từ tất cả các hàng
    document.querySelectorAll('.product-table tbody tr').forEach(row => {
        row.classList.remove('selected-row');
    });
}

// Toggle hiển thị thông tin chi tiết hàng hóa
window.toggleGoodsDetail = function(goodsId, element) {
    const detailRow = document.getElementById(`detail-row-goods-${goodsId}`);
    if (!detailRow) return;
    const isVisible = detailRow.style.display !== 'none';
    
    // Đóng tất cả các detail rows khác
    hideAllDetailRows();
    
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
    if (confirm(`Bạn có chắc chắn muốn xóa hàng hóa "${goodsName}" (${goodsCode})?`)) {
        // Tạo form để submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/goods/${goodsId}`;
        
        // Thêm CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Thêm method DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
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
    let visibleCount = 0; // Đếm số hàng hiển thị
    
    productRows.forEach(row => {
        let showRow = true;
        
        // Lọc theo loại sản phẩm
        if (productType) {
            const isMedicine = row.classList.contains('medicine-row');
            const isGoods = row.classList.contains('goods-row');
            const isService = row.classList.contains('service-row');
            
            if (productType === 'medicine' && !isMedicine) {
                showRow = false;
            } else if (productType === 'goods' && !isGoods) {
                showRow = false;
            } else if (productType === 'service' && !isService) {
                showRow = false;
            }
        }
        
        // Lọc theo vị trí (nếu có data-position-id)
        if (positionId && showRow) {
            const rowPositionId = row.getAttribute('data-position-id');
            // Nếu là dịch vụ hoặc có position ID khác thì ẩn
            if (!rowPositionId || rowPositionId !== positionId.toString()) {
                showRow = false;
            }
        }
        
        // Lọc theo nhà cung cấp (nếu có data-manufacturer-id)
        if (manufacturerId && showRow) {
            const rowManufacturerId = row.getAttribute('data-manufacturer-id');
            // Nếu là dịch vụ hoặc có manufacturer ID khác thì ẩn
            if (!rowManufacturerId || rowManufacturerId !== manufacturerId.toString()) 
            {
                showRow = false;
            }
        }
        
        // Lọc theo nhóm hàng (nếu có data-category-id)
        if (categoryId && showRow) {
            const rowCategoryId = row.getAttribute('data-category-id');
            if (rowCategoryId && rowCategoryId !== categoryId.toString()) {
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
            visibleCount++; // Tăng số đếm
        } else {
            row.style.display = 'none';
        }
    });
      
    // Hiển thị thông báo "không có kết quả" nếu cần
    showNoResultsMessage(visibleCount === 0);
    
    // Cập nhật số lượng sản phẩm hiển thị
    updateProductCount();
}

// Hiển thị thông báo "không có kết quả"
window.showNoResultsMessage = function(showMessage) {
    const tbody = document.querySelector('.product-table tbody');
    let noResultsRow = tbody.querySelector('.no-results-row');
    
    if (showMessage) {
        // Tạo thông báo "không có kết quả" nếu chưa có
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            noResultsRow.innerHTML = `
                <td colspan="9" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <p class="mb-2">Không tìm thấy sản phẩm nào phù hợp</p>
                        <small>Vui lòng thử lại với bộ lọc khác</small>
                    </div>
                </td>
            `;
            tbody.appendChild(noResultsRow);
        }
        noResultsRow.style.display = '';
    } else {
        // Ẩn thông báo nếu có kết quả
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    }
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
    // Ẩn tất cả detail rows khi trang load
    hideAllDetailRows();
    
    // Cập nhật số lượng sản phẩm ban đầu
    updateProductCount();
    
    // Thêm CSS cho thông báo không có kết quả
    const style = document.createElement('style');
    style.textContent = `
        .no-results-row {
            background-color: #f8f9fa;
        }
        
        .no-results-row td {
            border: none !important;
            padding: 3rem 1rem !important;
        }
        
        .no-results-row .text-muted {
            color: #6c757d !important;
        }
        
        .no-results-row i {
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .no-results-row p {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .no-results-row small {
            font-size: 0.9rem;
            opacity: 0.8;
        }
    `;
    document.head.appendChild(style);
}); 


// Search products function
window.searchProducts = function() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const productRows = document.querySelectorAll('.product-row');
    let visibleCount = 0;
    
    if (searchTerm === '') {
        // Nếu không có từ khóa tìm kiếm, hiển thị tất cả và xóa highlight
        productRows.forEach(row => {
            row.style.display = '';
            visibleCount++;
            // Xóa highlight khi không có từ khóa
            const productNameElement = row.querySelector('.product-name');
            const productCodeElement = row.querySelector('.product-code');
            
            if (productNameElement) {
                productNameElement.innerHTML = productNameElement.textContent;
            }
            if (productCodeElement) {
                productCodeElement.innerHTML = productCodeElement.textContent;
            }
        });
        showNoResultsMessage(false); // Ẩn thông báo không có kết quả
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
            visibleCount++;
            // Highlight từ khóa tìm kiếm
            highlightSearchTerm(row, searchTerm);
        } else {
            row.style.display = 'none';
        }
    });
    
    // Hiển thị thông báo "không có kết quả" nếu cần
    showNoResultsMessage(visibleCount === 0);
    
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
    
    // Xóa highlight và hiển thị tất cả
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
    
    // Ẩn thông báo không có kết quả
    showNoResultsMessage(false);
    
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
