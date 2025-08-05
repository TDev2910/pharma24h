document.addEventListener('DOMContentLoaded', function() {
    console.log('Goods management loaded');
    
    // Debug goods modal
    const goodsModal = document.getElementById('createGoodsModal');
    if (goodsModal) {
        console.log('Goods modal found:', goodsModal);
    } else {
        console.log('Goods modal not found');
    }
    
    // Test goods modal trigger
    const createGoodsLink = document.querySelector('[data-bs-target="#createGoodsModal"]');
    if (createGoodsLink) {
        console.log('Create goods link found:', createGoodsLink);
        createGoodsLink.addEventListener('click', function(e) {
            console.log('Create goods link clicked');
        });
    } else {
        console.log('Create goods link not found');
    }
    
    // Debug goods form submission
    const goodsForm = document.querySelector('#createGoodsModal form');
    if (goodsForm) {
        console.log('Goods form found:', goodsForm);
        goodsForm.addEventListener('submit', function(e) {
            console.log('Goods form submitted');
            const formData = new FormData(this);
            console.log('Goods form data:', Object.fromEntries(formData));
        });
    } else {
        console.log('Goods form not found');
    }
});

// Toggle hiển thị thông tin chi tiết hàng hóa
window.toggleGoodsDetail = function(goodsId, element) {
    const detailRow = document.getElementById(`detail-row-${goodsId}`);
    if (!detailRow) return;
    
    const isVisible = detailRow.style.display !== 'none';
    
    // Đóng tất cả các detail rows khác
    document.querySelectorAll('.detail-row').forEach(row => {
        row.style.display = 'none';
    });
    
    // Xóa highlight từ tất cả các hàng
    document.querySelectorAll('.goods-table tbody tr').forEach(row => {
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

// Delete goods function
window.deleteGoods = function(goodsId) {
    if (confirm('Bạn có chắc chắn muốn xóa hàng hóa này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/goods/${goodsId}`;
        
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

// Edit goods function
window.openEditGoodsModal = function(goodsId) {
    console.log('Opening edit modal for goods ID:', goodsId);
    
    fetch(`/admin/goods/${goodsId}/detail`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const goods = data.product;
                console.log('Goods data:', goods);
                
                // Populate form fields for goods
                const fields = {
                    'edit_ma_hang': goods.ma_hang || '',
                    'edit_ma_vach': goods.ma_vach || '',
                    'edit_ten_hang_hoa': goods.ten_hang_hoa || '',
                    'edit_nhom_hang_id': goods.nhom_hang_id || '',
                    'edit_gia_von': goods.gia_von || 0,
                    'edit_gia_ban': goods.gia_ban || 0,
                    'edit_ton_kho': goods.ton_kho || 0,
                    'edit_ton_thap_nhat': goods.ton_thap_nhat || 0,
                    'edit_ton_cao_nhat': goods.ton_cao_nhat || 999999999,
                    'edit_manufacturer_select': goods.manufacturer_id || '',
                    'edit_nuoc_san_xuat': goods.nuoc_san_xuat || '',
                    'edit_quy_cach_dong_goi': goods.quy_cach_dong_goi || '',
                    'edit_position_select': goods.position_id || '',
                    'edit_trong_luong': goods.trong_luong || 0,
                    'edit_don_vi_tinh': goods.don_vi_tinh || '',
                    'edit_mo_ta': goods.mo_ta || ''
                };
                
                // Set form values
                Object.keys(fields).forEach(fieldId => {
                    const element = document.getElementById(fieldId);
                    if (element) {
                        element.value = fields[fieldId];
                        console.log(`Set ${fieldId} to:`, fields[fieldId]);
                    } else {
                        console.log(`Element not found: ${fieldId}`);
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
                    console.log('Form action set to:', form.action);
                } else {
                    console.log('Form not found: editGoodsForm');
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
                    console.log('Goods modal opened successfully');
                } else {
                    console.log('Modal element not found: editGoodsModal');
                    alert('Không tìm thấy modal chỉnh sửa hàng hóa!');
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

// Delete confirmation modal functions
window.showDeleteConfirmation = function(goodsId, goodsCode, goodsName) {
    // Set modal content
    document.getElementById('deleteGoodsId').value = goodsId;
    document.getElementById('deleteGoodsCode').textContent = goodsCode;
    document.getElementById('deleteGoodsName').textContent = goodsName;
    
    // Show modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteModal.show();
}

window.confirmDelete = function() {
    const goodsId = document.getElementById('deleteGoodsId').value;
    const form = document.getElementById('deleteGoodsForm');
    form.action = `/admin/goods/${goodsId}`;
    form.submit();
}