<div class="row">
    <!-- Cột trái -->
    <div class="col-md-6">
        <!-- Tên nhà cung cấp -->
        <div class="mb-3">
            <label for="ten_nha_cung_cap" class="form-label">
                <i></i>Tên nhà cung cấp <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="ten_nha_cung_cap" name="ten_nha_cung_cap" 
                   value="{{ old('ten_nha_cung_cap') }}" 
                   required maxlength="255">
            <div class="invalid-feedback"></div>
        </div>

        <!-- Mã nhà cung cấp -->
        <div class="mb-3">
            <label for="ma_nha_cung_cap" class="form-label">
                <i></i>Mã nhà cung cấp <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="ma_nha_cung_cap" name="ma_nha_cung_cap" 
                   value="{{ old('ma_nha_cung_cap') }}" 
                   required maxlength="50" placeholder="VD: NCC001">
            <div class="form-text">Mã định danh duy nhất trong hệ thống</div>
            <div class="invalid-feedback"></div>
        </div>

        <!-- Điện thoại -->
        <div class="mb-3">
            <label for="dien_thoai" class="form-label">
                <i></i>Điện thoại <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="dien_thoai" name="dien_thoai" 
                   value="{{ old('dien_thoai') }}" 
                   required maxlength="20" placeholder="0123 456 789">
            <div class="invalid-feedback"></div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i></i>Email
            </label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="{{ old('email') }}" 
                   maxlength="100" placeholder="supplier@example.com">
            <div class="invalid-feedback"></div>
        </div>

        <!-- Nhóm nhà cung cấp -->
        <div class="mb-3">
            <label for="nhom_nha_cung_cap_id" class="form-label">
                <i class="fas fa-tags text-primary me-1"></i>Nhóm nhà cung cấp <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="nhom_nha_cung_cap_id" name="nhom_nha_cung_cap_id" required>
                <option value="">-- Chọn nhóm --</option>
                @foreach($supplierGroups as $group)
                    <option value="{{ $group->id }}" 
                            {{ old('nhom_nha_cung_cap_id') == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <!-- Cột phải -->
    <div class="col-md-6">
        <!-- Địa chỉ -->
        <div class="mb-3">
            <label for="dia_chi" class="form-label">
                <i></i>Địa chỉ chi tiết <span class="text-danger">*</span>
            </label>
            <textarea class="form-control" id="dia_chi" name="dia_chi" rows="2" required 
                placeholder="Số nhà, đường, phố...">{{ old('dia_chi') }}</textarea>
            <div class="invalid-feedback"></div>
        </div>

        <!-- Khu vực -->
        <div class="mb-3">
            <label for="khu_vuc" class="form-label">
                <i></i>Tỉnh/Thành phố <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="khu_vuc" name="khu_vuc" required>
                <option value="">-- Chọn tỉnh/thành --</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>
        
        <!-- Quận/Huyện -->
        <div class="mb-3">  
            <label for="phuong_xa" class="form-label">
                <i></i>Quận/Huyện <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="phuong_xa" name="phuong_xa" required disabled>
                <option value="">-- Chọn tỉnh/thành trước --</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>

        <!-- Tên công ty (xuất hóa đơn) -->
        <div class="mb-3">
            <label for="ten_cong_ty" class="form-label">
                <i></i>Tên công ty (xuất hóa đơn)
            </label>
            <input type="text" class="form-control" id="ten_cong_ty" name="ten_cong_ty" 
                   value="{{ old('ten_cong_ty') }}" 
                   maxlength="255" placeholder="Công ty TNHH ABC">
            <div class="form-text">Tên chính thức cho việc xuất hóa đơn</div>
            <div class="invalid-feedback"></div>
        </div>

        <!-- Mã số thuế -->
        <div class="mb-3">
            <label for="ma_so_thue" class="form-label">
                <i></i>Mã số thuế
            </label>
            <input type="text" class="form-control" id="ma_so_thue" name="ma_so_thue" 
                   value="{{ old('ma_so_thue') }}" 
                   maxlength="20" placeholder="0123456789">
            <div class="form-text">MST duy nhất theo pháp luật</div>
            <div class="invalid-feedback"></div>
        </div>
    </div>
</div>

<!-- Ghi chú -->
<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <label for="ghi_chu" class="form-label">
                <i></i>Ghi chú
            </label>
            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3" 
            placeholder="Ghi chú bổ sung về nhà cung cấp...">{{ old('ghi_chu') }}</textarea>
        </div>
    </div>
</div>

<!-- Trạng thái -->
<div class="row">
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="trang_thai" name="trang_thai" value="active" 
                   {{ old('trang_thai', 'active') == 'active' ? 'checked' : '' }}>
            <label class="form-check-label" for="trang_thai">
                <i></i>Kích hoạt ngay
            </label>
        </div>
    </div>
</div>