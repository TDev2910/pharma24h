<!-- Modal tạo thuốc -->
<div class="modal fade" id="createMedicineModal" tabindex="-1" aria-labelledby="createMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createMedicineModalLabel">Tạo thuốc mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.medicines.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Thông tin cơ bản -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Thông tin cơ bản</h6>
                            
                            <div class="mb-3">
                                <label for="ma_hang" class="form-label">Mã hàng</label>
                                <input type="text" class="form-control" id="ma_hang" name="ma_hang" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ma_vach" class="form-label">Mã vạch</label>
                                <input type="text" class="form-control" id="ma_vach" name="ma_vach">
                            </div>
                            
                            <div class="mb-3">
                                <label for="ten_thuoc" class="form-label">Tên thuốc</label>
                                <input type="text" class="form-control" id="ten_thuoc" name="ten_thuoc" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ten_viet_tat" class="form-label">Tên viết tắt</label>
                                <input type="text" class="form-control" id="ten_viet_tat" name="ten_viet_tat">
                            </div>
                            
                            <div class="mb-3">
                                <label for="nhom_hang_id" class="form-label">Nhóm hàng</label>
                                <select class="form-select" id="nhom_hang_id" name="nhom_hang_id" required>
                                    <option value="">Chọn nhóm hàng</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="gia_von" class="form-label">Giá vốn</label>
                                <input type="number" class="form-control" id="gia_von" name="gia_von" min="0" step="1000">
                            </div>
                            
                            <div class="mb-3">
                                <label for="gia_ban" class="form-label">Giá bán</label>
                                <input type="number" class="form-control" id="gia_ban" name="gia_ban" min="0" step="1000" required>
                            </div>
                        </div>
                        
                        <!-- Thông tin thuốc -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Thông tin thuốc</h6>
                            
                            <div class="mb-3">
                                <label for="so_dang_ky" class="form-label">Số đăng ký</label>
                                <input type="text" class="form-control" id="so_dang_ky" name="so_dang_ky">
                            </div>
                            
                            <div class="mb-3">
                                <label for="hoat_chat" class="form-label">Hoạt chất</label>
                                <input type="text" class="form-control" id="hoat_chat" name="hoat_chat">
                            </div>
                            
                            <div class="mb-3">
                                <label for="ham_luong" class="form-label">Hàm lượng</label>
                                <input type="text" class="form-control" id="ham_luong" name="ham_luong">
                            </div>
                            
                            <div class="mb-3">
                                <label for="duong_dung_select" class="form-label">Đường dùng</label>
                                <select class="form-select" id="duong_dung_select" name="drugusage_id">
                                    <option value="">Chọn đường dùng</option>
                                    @foreach($drugRoutes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="manufacturer_select" class="form-label">Hãng sản xuất</label>
                                <select class="form-select" id="manufacturer_select" name="manufacturer_id" required onchange="handleManufacturerChange(this)">
                                    <option value="">Chọn hãng sản xuất</option>
                                    @foreach($manufacturers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nuoc_san_xuat" class="form-label">Nước sản xuất</label>
                                <input type="text" class="form-control" id="nuoc_san_xuat" name="nuoc_san_xuat" value="Việt Nam">
                            </div>
                            
                            <div class="mb-3">
                                <label for="quy_cach_dong_goi" class="form-label">Quy cách đóng gói</label>
                                <input type="text" class="form-control" id="quy_cach_dong_goi" name="quy_cach_dong_goi">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Thông tin kho -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Thông tin kho</h6>
                            
                            <div class="mb-3">
                                <label for="ton_thap_nhat" class="form-label">Tồn kho tối thiểu</label>
                                <input type="number" class="form-control" id="ton_thap_nhat" name="ton_thap_nhat" min="0" value="0">
                            </div>
                            
                            <div class="mb-3">
                                <label for="ton_cao_nhat" class="form-label">Tồn kho tối đa</label>
                                <input type="number" class="form-control" id="ton_cao_nhat" name="ton_cao_nhat" min="0" value="999999999">
                            </div>
                            
                            <div class="mb-3">
                                <label for="position_select" class="form-label">Vị trí</label>
                                <select class="form-select" id="position_select" name="position_id">
                                    <option value="">Chọn vị trí</option>
                                    @foreach($positions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="trong_luong" class="form-label">Trọng lượng (g)</label>
                                <input type="number" class="form-control" id="trong_luong" name="trong_luong" min="0" step="0.1">
                            </div>
                            
                            <div class="mb-3">
                                <label for="don_vi_tinh_input" class="form-label">Đơn vị tính</label>
                                <input type="text" class="form-control" id="don_vi_tinh_input" name="don_vi_tinh" value="Viên">
                            </div>
                        </div>
                        
                        <!-- Thông tin khác -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Thông tin khác</h6>
                            
                            <div class="mb-3">
                                <label for="mo_ta" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="ban_truc_tiep" name="ban_truc_tiep" value="1">
                                    <label class="form-check-label" for="ban_truc_tiep">
                                        Bán trực tiếp
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
                    <button type="submit" class="btn btn-success">Lưu thuốc</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleManufacturerChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        console.log('Selected manufacturer:', selectedOption.text);
    }
}
</script> 