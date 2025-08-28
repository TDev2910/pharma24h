<!-- Modal Sửa Nhà Cung Cấp -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editSupplierModalLabel">
                    <i class="fas fa-edit me-2"></i>Chỉnh Sửa Nhà Cung Cấp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editSupplierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Form tĩnh như Medicine - ĐƠN GIẢN -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên nhà cung cấp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_ten_nha_cung_cap" name="ten_nha_cung_cap" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mã nhà cung cấp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_ma_nha_cung_cap" name="ma_nha_cung_cap" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_dien_thoai" name="dien_thoai" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nhóm nhà cung cấp <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_nhom_nha_cung_cap_id" name="nhom_nha_cung_cap_id" required>
                                    <option value="">-- Chọn nhóm --</option>
                                    @foreach($supplierGroups ?? [] as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit_dia_chi" name="dia_chi" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_khu_vuc" name="khu_vuc" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_phuong_xa" name="phuong_xa" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tên công ty (xuất hóa đơn)</label>
                                <input type="text" class="form-control" id="edit_ten_cong_ty" name="ten_cong_ty">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mã số thuế</label>
                                <input type="text" class="form-control" id="edit_ma_so_thue" name="ma_so_thue">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="edit_ghi_chu" name="ghi_chu" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
