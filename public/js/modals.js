function openModal(modalId) {
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}

// Close modal function
function closeModal(modalId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
    if (modal) {
        modal.hide();
    }
}

// Close all modals
function closeAllModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    });
}

// Show loading state in modal
function showModalLoading(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('modal-loading');
    }
}

// Hide loading state in modal
function hideModalLoading(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('modal-loading');
    }
}

// Show delete confirmation modal
function showDeleteConfirmation(productId, productCode, productName, type = 'medicine') {
    // Set product info
    document.getElementById('deleteMedicineCode').textContent = productCode;
    document.getElementById('deleteMedicineName').textContent = productName;
    
    // Set form action
    const form = document.getElementById('deleteMedicineForm');
    if (form) {
        form.action = `/admin/${type}s/${productId}`;
    }
    
    // Open modal
    openModal('deleteConfirmationModal');
}

// Confirm delete function
function confirmDelete() {
    const form = document.getElementById('deleteMedicineForm');
    if (form) {
        form.submit();
    }
}

// Open unit modal
function openUnitModal() {
    openModal('unitModal');
}

// Create new unit
function createNewUnit() {
    const unitName = document.getElementById('unitName').value.trim();
    if (!unitName) {
        alert('Vui lòng nhập tên đơn vị!');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', unitName);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch('/admin/products/unit', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.unit) {
            // Add new unit to select options
            const select = document.getElementById('don_vi_tinh_input');
            if (select) {
                const newOption = document.createElement('option');
                newOption.value = data.unit.id;
                newOption.textContent = data.unit.name;
                select.appendChild(newOption);
                select.value = data.unit.id;
            }
            
            // Close modal
            closeModal('unitModal');
            
            // Show success message
            showSuccessMessage('Tạo đơn vị thành công!');
        } 
        else 
        {
            alert(data.message || 'Có lỗi xảy ra khi tạo đơn vị!');
        }
    })
    .catch(error => {
        // Error creating unit
        alert('Đã xảy ra lỗi mạng!');
    });
}

// Open category modal
function openCategoryModal() {
    openModal('createCategoryModal');
}

// Create new category
function createNewCategory() {
    const categoryName = document.getElementById('category-name').value.trim();
    if (!categoryName) {
        alert('Vui lòng nhập tên nhóm hàng!');
        return;
    }
    
    const form = document.getElementById('createCategoryForm');
    if (form) {
        form.submit();
    }
}

// Handle modal shown event
function onModalShown(modalId, callback) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.addEventListener('shown.bs.modal', callback);
    }
}

// Handle modal hidden event
function onModalHidden(modalId, callback) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.addEventListener('hidden.bs.modal', callback);
    }
}

// Handle modal show event
function onModalShow(modalId, callback) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.addEventListener('show.bs.modal', callback);
    }
}

// Handle modal hide event
function onModalHide(modalId, callback) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.addEventListener('hide.bs.modal', callback);
    }
}

// Check if modal is open
function isModalOpen(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        return modal.classList.contains('show');
    }
    return false;
}

// Get current open modal
function getCurrentOpenModal() {
    const openModals = document.querySelectorAll('.modal.show');
    return openModals.length > 0 ? openModals[0] : null;
}

// Disable modal backdrop
function disableModalBackdrop(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.setAttribute('data-bs-backdrop', 'static');
        modal.setAttribute('data-bs-keyboard', 'false');
    }
}

// Enable modal backdrop
function enableModalBackdrop(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.removeAttribute('data-bs-backdrop');
        modal.removeAttribute('data-bs-keyboard');
    }
}

// Set modal size
function setModalSize(modalId, size) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const dialog = modal.querySelector('.modal-dialog');
        if (dialog) {
            dialog.className = `modal-dialog modal-${size}`;
        }
    }
}

// Fade in modal
function fadeInModal(modalId, duration = 300) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.transition = `opacity ${duration}ms ease-in-out`;
        modal.style.opacity = '0';
        modal.style.display = 'block';
        
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);
    }
}

// Fade out modal
function fadeOutModal(modalId, duration = 300) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.transition = `opacity ${duration}ms ease-in-out`;
        modal.style.opacity = '0';
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, duration);
    }
}

// Update modal content
function updateModalContent(modalId, content) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const body = modal.querySelector('.modal-body');
        if (body) {
            body.innerHTML = content;
        }
    }
}

// Update modal title
function updateModalTitle(modalId, title) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const titleElement = modal.querySelector('.modal-title');
        if (titleElement) {
            titleElement.textContent = title;
        }
    }
}

// Update modal footer
function updateModalFooter(modalId, content) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const footer = modal.querySelector('.modal-footer');
        if (footer) {
            footer.innerHTML = content;
        }
    }
}

// Reset modal form
function resetModalForm(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Validate modal form
function validateModalForm(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector('form');
        if (form) {
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
    }
    return false;
}

// Submit modal form
function submitModalForm(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector('form');
        if (form && validateModalForm(modalId)) {
            form.submit();
        }
    }
}

// Show success message
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