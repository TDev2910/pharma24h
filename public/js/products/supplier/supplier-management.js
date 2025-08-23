document.addEventListener('DOMContentLoaded', function() {
    // Initialize supplier management functionality
    console.log('Supplier management JS loaded');
});

// Toggle hiển thị thông tin chi tiết supplier
window.toggleSupplierDetail = function(supplierId, element) {
    const detailRow = document.getElementById(`detail-row-${supplierId}`);
    if (!detailRow) return;
    
    const isVisible = detailRow.style.display !== 'none';
    
    // Đóng tất cả các detail rows khác
    document.querySelectorAll('.detail-row').forEach(row => {
        row.style.display = 'none';
    });
    
    // Xóa highlight từ tất cả các hàng
    document.querySelectorAll('.supplier-row').forEach(row => {
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

// Xử lý click vào checkbox (không trigger detail)
document.addEventListener('click', function(e) {
    if (e.target.type === 'checkbox' && e.target.classList.contains('form-check-input')) {
        e.stopPropagation();
    }
});

// Xử lý các action buttons trong detail
document.addEventListener('click', function(e) {
    // Chỉnh sửa supplier
    if (e.target.closest('.btn-success') && e.target.closest('.supplier-detail-container')) {
        e.stopPropagation();
        const supplierId = e.target.closest('.detail-row').id.replace('detail-row-', '');
        editSupplier(supplierId);
    }
    
    // In thông tin supplier
    if (e.target.closest('.btn-primary') && e.target.closest('.supplier-detail-container')) {
        e.stopPropagation();
        const supplierId = e.target.closest('.detail-row').id.replace('detail-row-', '');
        printSupplier(supplierId);
    }
    
    // Xóa supplier
    if (e.target.closest('.btn-danger') && e.target.closest('.supplier-detail-container')) {
        e.stopPropagation();
        const supplierId = e.target.closest('.detail-row').id.replace('detail-row-', '');
        deleteSupplier(supplierId);
    }
});

// Function chỉnh sửa supplier
function editSupplier(supplierId) {
    // TODO: Implement edit functionality
    console.log('Edit supplier:', supplierId);
    alert('Chức năng chỉnh sửa sẽ được phát triển sau!');
}

// Function in thông tin supplier
function printSupplier(supplierId) {
    // TODO: Implement print functionality
    console.log('Print supplier:', supplierId);
    alert('Chức năng in thông tin sẽ được phát triển sau!');
}

// Function xóa supplier
function deleteSupplier(supplierId) {
    if (confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?')) {
        // TODO: Implement delete functionality
        console.log('Delete supplier:', supplierId);
        alert('Chức năng xóa sẽ được phát triển sau!');
    }
}