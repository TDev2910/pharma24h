/**
 * Service Management JavaScript
 * Handles specific service operations like edit modal
 */

/**
 * Open edit service modal and populate with data
 */
window.openEditServiceModal = function(serviceId) {
    console.log('Opening edit modal for service:', serviceId);
    
    // Fetch service data
    fetch(`/admin/services/${serviceId}/detail`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.service) {
                populateEditServiceForm(data.service);
                
                // Update form action
                const form = document.getElementById('editServiceForm');
                if (form) {
                    form.action = `/admin/services/${serviceId}`;
                }
                
                // Show current image if exists
                if (data.service.image) {
                    const previewContainer = document.getElementById('editImagePreview');
                    const previewImg = document.getElementById('editPreviewImg');
                    if (previewContainer && previewImg) {
                        previewImg.src = `/storage/${data.service.image}`;
                        previewContainer.style.display = 'block';
                    }
                }
                
                // Show modal
                const modalElement = document.getElementById('editServiceModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    alert('Không tìm thấy modal chỉnh sửa!');
                }
            } else {
                alert('Không thể tải thông tin dịch vụ!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi tải thông tin dịch vụ!');
        });
};

/**
 * Populate edit service form with data
 */
function populateEditServiceForm(service) {
    // Populate basic fields
    const fields = {
        'service_edit_ma_dich_vu': service.ma_dich_vu,
        'service_edit_ten_dich_vu': service.ten_dich_vu,
        'service_edit_gia_ban': service.gia_ban,
        'service_edit_thoi_gian_thuc_hien': service.thoi_gian_thuc_hien,
        'service_edit_mo_ta': service.mo_ta,
        'service_edit_ghi_chu': service.ghi_chu
    };
    
    // Set text input values
    Object.keys(fields).forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element && fields[fieldId] !== null) {
            element.value = fields[fieldId] || '';
        }
    });
    
    // Set select values
    const selectFields = {
        'nhom_dich_vu_id': service.nhom_dich_vu_id,
        'service_edit_hinh_thuc': service.hinh_thuc,
        'service_edit_trang_thai': service.trang_thai
    };
    
    Object.keys(selectFields).forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element && selectFields[fieldId] !== null) {
            element.value = selectFields[fieldId] || '';
        }
    });
}

/**
 * Toggle service detail display
 */
window.toggleServiceDetail = function(serviceId, element) {
    const detailRow = document.getElementById(`detail-row-service-${serviceId}`);
    if (!detailRow) {
        console.error(`Detail row not found: detail-row-service-${serviceId}`);
        return;
    }
    
    const isVisible = detailRow.style.display !== 'none';
    
    // Close all other detail rows
    document.querySelectorAll('.detail-row').forEach(row => {
        row.style.display = 'none';
    });
    
    // Remove highlight from all rows
    document.querySelectorAll('.product-table tbody tr').forEach(row => {
        row.classList.remove('selected-row');
    });
    
    if (!isVisible) {
        detailRow.style.display = 'table-row';
        element.classList.add('selected-row');
    }
};

/**
 * Delete service with confirmation
 */
window.deleteService = function(serviceId, serviceName) {
    if (confirm(`Bạn có chắc chắn muốn xóa dịch vụ "${serviceName}"?`)) {
        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/services/${serviceId}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        // Add method override
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
};

/**
 * Print service label
 */
window.printServiceLabel = function(serviceId) {
    alert('Tính năng in tem mã dịch vụ sẽ được phát triển trong tương lai!');
};

/**
 * Update service status
 */
window.updateServiceStatus = function(serviceId, newStatus) {
    fetch(`/admin/services/${serviceId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            trang_thai: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload to show updated status
        } else {
            alert('Có lỗi xảy ra khi cập nhật trạng thái!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái!');
    });
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Service management JavaScript loaded');
    
    // Test if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.warn('Bootstrap is not loaded');
    }
});
