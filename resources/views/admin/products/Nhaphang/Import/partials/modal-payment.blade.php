<!-- Modal: Thanh toán nhà cung cấp -->
<div class="modal fade" id="paySupplierModal" tabindex="-1" aria-labelledby="paySupplierLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paySupplierLabel">Tiền trả nhà cung cấp</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Thanh toán</label>
          <input type="number" class="form-control form-control-lg text-end" id="payModalAmount" value="0" min="0">
        </div>
        <div class="d-flex gap-2 mb-3">
          <button type="button" class="btn btn-outline-primary flex-fill pay-method active" data-method="cash">Tiền mặt</button>
          <button type="button" class="btn btn-outline-primary flex-fill pay-method" data-method="card">Thẻ</button>
          <button type="button" class="btn btn-outline-primary flex-fill pay-method" data-method="transfer">Chuyển khoản</button>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Cần trả nhà cung cấp</span>
          <span id="payModalPayable" class="fw-bold">0</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Tiền trả nhà cung cấp</span>
          <span id="payModalPaid" class="fw-bold">0</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
        <button type="button" class="btn btn-primary" id="payModalConfirm">Xong</button>
      </div>
    </div>
  </div>
</div>

<style>
    .modal-backdrop { z-index: 9998 !important; }
    .modal { z-index: 9999 !important; pointer-events: auto !important; }
</style>


