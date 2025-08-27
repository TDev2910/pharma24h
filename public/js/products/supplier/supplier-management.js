/**
 * SUPPLIER MANAGEMENT JAVASCRIPT
 * Functions for supplier table interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize supplier count
    updateSupplierCount(document.querySelectorAll('.supplier-row').length);
});

/**
 * Toggle supplier detail row
 */
function toggleSupplierDetail(supplierId, rowElement) {
    const detailRow = document.getElementById(`detail-row-${supplierId}`);
    const allDetailRows = document.querySelectorAll('.detail-row');
    const allSupplierRows = document.querySelectorAll('.supplier-row');
    
    // Close all other detail rows
    allDetailRows.forEach(row => {
        if (row.id !== `detail-row-${supplierId}`) {
            row.style.display = 'none';
        }
    });
    
    // Remove selected class from all rows
    allSupplierRows.forEach(row => {
        row.classList.remove('selected-row');
    });
    
    // Toggle current detail row
    if (detailRow.style.display === 'none' || detailRow.style.display === '') {
        detailRow.style.display = 'table-row';
        rowElement.classList.add('selected-row');
    } else {
        detailRow.style.display = 'none';
        rowElement.classList.remove('selected-row');
    }
}

/**
 * Filter suppliers function
 */
function filterSuppliers() {
    const groupId = document.querySelector('select[name="supplier_group_id"]').value;
    const status = document.querySelector('select[name="status"]').value;
    const province = document.querySelector('select[name="province"]').value;
    const businessType = document.querySelector('select[name="business_type"]').value;
    const rating = document.querySelector('select[name="rating"]').value;
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    
    const supplierRows = document.querySelectorAll('.supplier-row');
    let visibleCount = 0;
    
    supplierRows.forEach(row => {
        let showRow = true;
        
        // Filter by group
        if (groupId && row.getAttribute('data-group-id') !== groupId) {
            showRow = false;
        }
        
        // Filter by status
        if (status && row.getAttribute('data-status') !== status) {
            showRow = false;
        }
        
        // Filter by province
        if (province && row.getAttribute('data-province') !== province) {
            showRow = false;
        }
        
        // Filter by business type
        if (businessType && row.getAttribute('data-business-type') !== businessType) {
            showRow = false;
        }
        
        // Filter by rating
        if (rating && parseInt(row.getAttribute('data-rating')) < parseInt(rating)) {
            showRow = false;
        }
        
        // Filter by search term
        if (searchTerm && showRow) {
            const supplierName = row.querySelector('.supplier-name')?.textContent.toLowerCase() || '';
            const supplierCode = row.querySelector('.supplier-code')?.textContent.toLowerCase() || '';
            
            const isMatch = supplierName.includes(searchTerm) || supplierCode.includes(searchTerm);
            
            if (!isMatch) {
                showRow = false;
            }
        }
        
        // Show/hide row
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update summary
    updateSupplierCount(visibleCount);
}

/**
 * Search suppliers function
 */
function searchSuppliers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const supplierRows = document.querySelectorAll('.supplier-row');
    let visibleCount = 0;
    
    supplierRows.forEach(row => {
        const supplierName = row.querySelector('.supplier-name')?.textContent.toLowerCase() || '';
        const supplierCode = row.querySelector('.supplier-code')?.textContent.toLowerCase() || '';
        
        const isMatch = supplierName.includes(searchTerm) || supplierCode.includes(searchTerm);
        
        if (isMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    updateSupplierCount(visibleCount);
}

/**
 * Update supplier count
 */
function updateSupplierCount(visibleCount) {
    const totalSuppliers = document.querySelectorAll('.supplier-row').length;
    const summaryElement = document.querySelector('.summary-section small');
    if (summaryElement) {
        summaryElement.innerHTML = `Tổng cộng: <strong>${totalSuppliers}</strong> nhà cung cấp | Hiển thị: <strong>${visibleCount}</strong>`;
    }
}

/**
 * Handle supplier category form submission
 */
document.getElementById('createSupplierCategoryForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('createCategoryBtn');
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo...';
});

/**
 * Clear form validation errors
 */
function clearFormErrors() {
    const errorElements = document.querySelectorAll('.invalid-feedback');
    const inputElements = document.querySelectorAll('.is-invalid');
    
    errorElements.forEach(el => el.textContent = '');
    inputElements.forEach(el => el.classList.remove('is-invalid'));
}

/**
 * Reset form when modal is hidden
 */
document.getElementById('createSupplierCategoryModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('createSupplierCategoryForm');
    form.reset();
    clearFormErrors();
});