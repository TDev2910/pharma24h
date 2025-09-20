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
            const formData = new FormData(this);
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
    // Redirect to edit page thay vì sử dụng modal
    window.location.href = `/admin/medicines/${medicineId}/edit`;
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

// Function cho nút xóa trong detail row
window.showDeleteMedicineConfirmation = function(medicineId, medicineCode, medicineName) {
    if (confirm(`Bạn có chắc chắn muốn xóa thuốc "${medicineName}" (${medicineCode})?`)) {
        // Tạo form để gửi request DELETE đến controller
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/medicines/${medicineId}`;
        
        // Thêm CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Thêm method override cho DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        
        // Submit form
        form.submit();
    }
}

// Confirm delete function for medicines
window.confirmDelete = function() {
    const medicineId = document.getElementById('deleteMedicineId').value;
    const form = document.getElementById('deleteMedicineForm');
    
    // Kiểm tra xem đây là thuốc hay hàng hóa dựa vào biến global
    if (window.isDeletingGoods === true) {
        // Đây là hàng hóa
        form.action = `/admin/goods/${medicineId}`;

    } else {
        // Đây là thuốc
        form.action = `/admin/medicines/${medicineId}`;

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

// Print label function
window.printLabel = function(medicineId) {
    // Tạm thời hiển thị thông báo, có thể implement sau
    alert('Chức năng in tem mã sẽ được triển khai sau!');
    // Hoặc có thể redirect đến trang in tem nếu có
    // window.open(`/admin/medicines/${medicineId}/print-label`, '_blank');
} 

//tim kiem san pham
window.searchProducts = function() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const productRows = document.querySelectorAll('.product-row');
    
    if (searchTerm === '') {
        // Nếu không có từ khóa tìm kiếm, hiển thị tất cả và xóa highlight
        productRows.forEach(row => {
            row.style.display = '';
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

// Tab functionality for product detail
window.switchTab = function(medicineId, tabName) {
    // Remove active class from all tabs
    const tabs = document.querySelectorAll(`#detail-row-${medicineId} .pd-tabs .tab`);
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Add active class to clicked tab
    const activeTab = event.target;
    activeTab.classList.add('active');
    
    // Hide all tab content
    const tabContents = document.querySelectorAll(`#detail-row-${medicineId} .tab-content`);
    tabContents.forEach(content => content.style.display = 'none');
    
    // Show selected tab content
    const targetContent = document.getElementById(`${tabName}-${medicineId}`);
    if (targetContent) {
        targetContent.style.display = 'block';
    }
} 