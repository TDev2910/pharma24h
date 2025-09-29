<form action="{{ route('admin.import.store') }}" method="POST" class="card shadow-sm summary-card">
  @csrf
  <input type="hidden" name="status" value="imported">
  <div class="card-body d-flex flex-column summary-right">

    <!-- Header tìm Ncc + nút thêm -->
    <div class="mb-3">
      <label class="form-label">Nhà cung cấp</label>
      <div class="d-flex gap-2">
        <select name="supplier_id" class="form-select">
          <option value="">Tìm nhà cung cấp</option>
          @foreach(($suppliers ?? []) as $s)
            <option value="{{ $s->id }}">{{ $s->ten_nha_cung_cap }} ({{ $s->ma_nha_cung_cap }})</option>
          @endforeach
        </select>
      </div>
    </div>

    <!-- Mã phiếu nhập -->
    <div class="mb-3 d-flex align-items-center">
      <label class="form-label mb-0 me-3" style="min-width: 130px;">Mã phiếu nhập</label>
      <div class="input-group">
        <input type="text" name="import_code" id="import_code" class="form-control text-muted" placeholder="Mã phiếu tự động" readonly>
        <button class="btn btn-outline-secondary" type="button" id="generateCodeBtn">
          <i class="fas fa-sync-alt"></i> Tạo mã
        </button>
      </div>
    </div>
  
    <!-- Ngày nhập hàng -->
    <div class="mb-3 d-flex align-items-center">
      <label class="form-label mb-0 me-3" style="min-width: 130px;">Ngày nhập</label>
      <input type="date" name="import_date" class="form-control" value="{{ old('import_date', now()->toDateString()) }}" style="max-width: 200px;">
    </div>

    <!-- Khu tổng tiền giống mẫu -->
    <div class="mt-2 pt-3" style="border-top:1px solid #e9ecef;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex align-items-center gap-2">
          <span class="form-label m-0">Tổng tiền hàng</span>
          <input type="number" name="subtotal_raw" class="form-control form-control-sm text-center" style="width:64px;background:#f8f9fa;" value="0" min="0">
        </div>
        <span id="summaryTotal" class="fw-bold value">0</span>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="form-label m-0">Giảm giá</span>
        <input type="number" name="discount" class="form-control text-end" style="width:120px;background:#fff;" value="0" min="0">
      </div>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="form-label m-0">Cần trả nhà cung cấp</span>
        <span id="summaryPayable" class="link-number">0</span>
      </div>

      <div class="mb-1 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span class="form-label m-0">Tiền trả nhà cung cấp</span>
          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paySupplierModal" title="Thanh toán">
            <i class="fas fa-credit-card"></i>
          </button>
        </div>
        <input type="number" name="cash_paid" id="cashPaid" class="form-control text-end" style="width:120px;" value="0" min="0">
      </div>

      <div class="d-flex justify-content-between align-items-center mt-2">
        <span class="form-label m-0">Tính vào công nợ</span>
        <span id="debtDisplay" class="value text-muted">0</span>
      </div>

      <input type="hidden" name="subtotal">
      <input type="hidden" name="payable">
    </div>

    <!-- Ghi chú -->
    <div class="mb-3 mt-3">
      <textarea name="note" rows="3" class="form-control" placeholder="Nhập ghi chú..."></textarea>
    </div>

   <!-- Buttons -->
  <div id="itemsHiddenHolder"></div>
  <div class="d-flex justify-content-end gap-2 mt-3">
    <button type="submit" name="action" value="draft" class="btn btn-primary px-4">
      <i class="fas fa-save me-1"></i>Lưu tạm
    </button>
    <button type="submit" name="action" value="complete" class="btn btn-success px-4">
      <i class="fas fa-check me-1"></i>Hoàn thành
    </button>
  </div>

  @push('scripts')
  <script>
    (function(){
      const subtotalRaw = document.querySelector('input[name="subtotal_raw"]');
      const discountEl  = document.querySelector('input[name="discount"]');
      const cashPaidEl  = document.getElementById('cashPaid');
      const totalText   = document.getElementById('summaryTotal');
      const payableText = document.getElementById('summaryPayable');
      const debtText    = document.getElementById('debtDisplay');
      const subtotalHid = document.querySelector('input[name="subtotal"]');
      const payableHid  = document.querySelector('input[name="payable"]');
  
      function recalc(){
        const sub = Number(subtotalRaw?.value || 0);
        const disc= Number(discountEl?.value || 0);
        const cash = Number(cashPaidEl?.value || 0);
        const total = Math.max(sub,0);
        const payable = Math.max(sub - disc,0);
        const debt = cash ? (cash * -1) : (payable * -1);
        if (totalText) totalText.textContent = total.toLocaleString('vi-VN');
        if (payableText) payableText.textContent = payable.toLocaleString('vi-VN');
        if (debtText) debtText.textContent = debt.toLocaleString('vi-VN');
        if (subtotalHid) subtotalHid.value = total;
        if (payableHid) payableHid.value = payable;
      }
      subtotalRaw?.addEventListener('input', recalc);
      discountEl?.addEventListener('input', recalc);
      cashPaidEl?.addEventListener('input', recalc);
      recalc();
  
      // Modal interactions
      const payModal = document.getElementById('paySupplierModal');
      const payAmount = document.getElementById('payModalAmount');
      const payPayable = document.getElementById('payModalPayable');
      const payPaid = document.getElementById('payModalPaid');
      const payConfirm = document.getElementById('payModalConfirm');
  
      payModal?.addEventListener('show.bs.modal', () => {
        const payableVal = Number(payableHid?.value || 0);
        payAmount.value = payableVal;
        payPayable.textContent = payableVal.toLocaleString('vi-VN');
        payPaid.textContent = Number(payAmount.value || 0).toLocaleString('vi-VN');
      });
  
      payAmount?.addEventListener('input', () => {
        payPaid.textContent = Number(payAmount.value || 0).toLocaleString('vi-VN');
      });
  
      document.querySelectorAll('.pay-method')?.forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.pay-method').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
        });
      });
  
      payConfirm?.addEventListener('click', () => {
        const val = Number(payAmount.value || 0);
        if (cashPaidEl) cashPaidEl.value = val;
        recalc();
        const modal = bootstrap.Modal.getInstance(payModal);
        modal?.hide();
      });
  
      // Thêm code tạo mã phiếu tự động
      const generateCodeBtn = document.getElementById('generateCodeBtn');
      const importCodeInput = document.getElementById('import_code');
  
      if (generateCodeBtn && importCodeInput) {
        // Tự động tạo mã khi trang tải
        generateCode();
        
        // Tạo mã mới khi click nút
        generateCodeBtn.addEventListener('click', function() {
          generateCode();
        });
      }
  
      function generateCode() {
        // Hiển thị loading
        generateCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo...';
        generateCodeBtn.disabled = true;
        
        // Gọi API để tạo mã từ server
        fetch('{{ route("admin.generate-import-code") }}')
          .then(response => response.json())
          .then(data => {
            importCodeInput.value = data.code;
            generateCodeBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Tạo mã';
            generateCodeBtn.disabled = false;
          })
          .catch(error => {
            console.error('Error:', error);
            // Fallback: tạo mã client-side
            importCodeInput.value = generateRandom7DigitCode();
            generateCodeBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Tạo mã';
            generateCodeBtn.disabled = false;
          });
      }
  
      function generateRandom7DigitCode() {
        return Math.floor(1000000 + Math.random() * 9000000);
      }
    })();
    
  </script>
  @endpush
</form>

