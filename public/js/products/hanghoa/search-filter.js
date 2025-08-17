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

// Filter products 
window.filterProducts = function() {
    // Lấy giá trị của các filter
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
        
        // Lọc theo loại sản phẩm (Thuốc/Hàng hóa/Dịch vụ)
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
            if (!rowManufacturerId || rowManufacturerId !== manufacturerId.toString()) {
                console.log(rowManufacturerId, manufacturerId);
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

// Initialize filters when page loads
document.addEventListener('DOMContentLoaded', function() {
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
