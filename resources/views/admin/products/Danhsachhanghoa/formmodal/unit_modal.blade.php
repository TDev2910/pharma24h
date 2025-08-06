<div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="unitModalLabel">
                    Thêm đơn vị cơ bản
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="unitForm">
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Đơn vị cơ bản là đơn vị bán phổ biến nhất hoặc đơn vị chính dùng để quản lý tồn kho
                    </p>
                    
                    <div class="mb-3">
                        <label for="unitName" class="form-label">Tên đơn vị cơ bản</label>
                        <input type="text" class="form-control" id="unitName" placeholder="Tên đơn vị cơ bản" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="unitPrice" class="form-label">Giá bán</label>
                        <input type="number" class="form-control" id="unitPrice" value="0" min="0" step="0.01">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="directSale" checked>
                        <label class="form-check-label" for="directSale">
                            Bán trực tiếp
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
                    <button type="submit" class="btn btn-success">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden fields để lưu thêm thông tin -->
<input type="hidden" id="unit_price_hidden" name="unit_price" value="0">
<input type="hidden" id="direct_sale_hidden" name="direct_sale" value="1">

<script>
// Unit Modal Functions
function openUnitModal() {
    const modal = new bootstrap.Modal(document.getElementById('unitModal'));
    modal.show();
}

function closeUnitModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('unitModal'));
    if (modal) {
        modal.hide();
    }
}

function saveUnit() {
    const unitName = document.getElementById('unitName').value.trim();
    const unitPrice = document.getElementById('unitPrice').value;
    const directSale = document.getElementById('directSale').checked;
    
    if (!unitName) {
        // Validation failed
        return;
    }
    
    // Kiểm tra xem đang ở form nào (create hay edit)
    const createInput = document.getElementById('don_vi_tinh_input');
    const editInput = document.getElementById('edit_don_vi_tinh_input');
    
    if (createInput && createInput.offsetParent !== null) {
        createInput.value = unitName; // Đang ở form create
        document.getElementById('unit_price_hidden').value = unitPrice;
        document.getElementById('direct_sale_hidden').value = directSale ? '1' : '0';
    } 
    else if (editInput && editInput.offsetParent !== null) { // Đang ở form edit
        editInput.value = unitName;
    }
    
    // Đóng modal
    closeUnitModal();
    
    // Hiển thị thông báo
    showSuccessMessage('Thiết lập đơn vị tính thành công!');
}

// Initialize unit modal
document.addEventListener('DOMContentLoaded', function() {
    const unitForm = document.getElementById('unitForm');
    if (unitForm) {
        unitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveUnit();
        });
    }
});
</script> 