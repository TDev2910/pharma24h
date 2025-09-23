<!-- Modal chỉnh sửa thuốc -->
<div class="modal fade" id="editMedicineModal" tabindex="-1" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header">
          <h5 class="modal-title" id="editMedicineModalLabel">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa thuốc
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editMedicineForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="modal-body p-4">
              <ul class="nav nav-tabs border-0 mb-3" id="editMedicineTab" role="tablist">
                  <li class="" role="presentation">
                      <button class="nav-link active px-4 py-2 fw-bold" id="edit-info-tab" data-bs-toggle="tab" data-bs-target="#edit-info" type="button" role="tab" style="margin-left:-25px;">Thông tin</button>
                  </li>
                  <li class="" role="presentation">
                      <button class="nav-link px-4 py-2 fw-bold" id="edit-desc-tab" data-bs-toggle="tab" data-bs-target="#edit-desc" type="button" role="tab" style="border-radius:8px;">Mô tả</button>
                  </li>
              </ul>
              <div class="tab-content" id="editMedicineTabContent">
                  <!-- Tab Thông tin -->
                  <div class="tab-pane fade show active" id="edit-info" role="tabpanel">
                      <!-- THÔNG TIN CƠ BẢN -->
                      <div class="row mb-4">
                          <!-- Inputs bên trái -->
                          <div class="col-md-8">
                              <div class="row g-2">
                                  <div class="col-md-6">
                                      <label class="form-label">Mã hàng</label>
                                      <input type="text" class="form-control" name="ma_hang" id="medicine_edit_ma_hang" placeholder="Nhập mã hàng">
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Mã vạch</label>
                                      <input type="text" class="form-control" name="ma_vach" id="medicine_edit_ma_vach" placeholder="Nhập mã vạch">
                                  </div>
                                  <div class="col-md-12">
                                      <label class="form-label">Tên thuốc <span class="text-danger">*</span></label>
                                      <input type="text" class="form-control" name="ten_thuoc" id="medicine_edit_ten_thuoc" placeholder="Nhập tên thuốc" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Tên viết tắt</label>
                                      <input type="text" class="form-control" name="ten_viet_tat" id="medicine_edit_ten_viet_tat" placeholder="Nhập tên viết tắt">
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Nhóm hàng <span class="text-danger">*</span></label>
                                      <select class="form-select" name="nhom_hang_id" id="medicine_edit_nhom_hang_id" required>
                                          <option value="">Chọn nhóm hàng (Bắt buộc)</option>
                                          @foreach($categories as $id => $name)
                                              <option value="{{ $id }}">{{ $name }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                              </div>
                          </div> 
                          <!-- Ảnh bên phải -->
                          <div class="col-md-4">
                              <label class="form-label fw-bold mb-3">
                                  <i class="fas fa-image me-2 text-primary"></i>Ảnh sản phẩm
                              </label>
                              <div class="border-2 border-dashed border-primary rounded-3 d-flex flex-column align-items-center justify-content-center position-relative bg-light" 
                                   style="width:200px;height:200px;background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="edit-medicine-image-preview-container">
                                  <input type="file" name="image" accept="image/*" 
                                      style="opacity:0;position:absolute;width:100%;height:100%;cursor:pointer;top:0;left:0;z-index:1;"
                                      onchange="previewEditMedicineImage(this)">
                                  <div class="text-center" id="edit-medicine-image-placeholder"> 
                                      <i class="fas fa-image fa-3x text-primary mb-3"></i>
                                      <div class="fw-bold text-primary mb-2">Thêm ảnh sản phẩm</div>
                                      <small class="text-muted">Click để chọn ảnh</small>
                                      <div class="mt-3">
                                          <span class="badge bg-light text-dark">Tối đa 2MB</span>
                                      </div>
                                  </div>
                                  <img id="edit-medicine-image-preview" src="" alt="Preview" 
                                       style="width:100%;height:100%;object-fit:cover;border-radius:8px;display:none;">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Giá vốn, giá bán -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Giá vốn, giá bán</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-6">
                                  <label class="form-label">Giá vốn <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control price-input"
                                        name="gia_von"
                                        id="medicine_edit_gia_von"
                                        required>                             
                                </div>
                            <div class="col-md-6">
                                  <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                  <div class="input-group">
                                      <input type="text"
                                            class="form-control price-input"
                                            name="gia_ban"
                                            id="medicine_edit_gia_ban"
                                            required>
                                      <button class="btn btn-outline-secondary" type="button">Thiết lập giá</button>
                                  </div>
                              </div>
                          </div>
                      </fieldset>

                      <!-- Thông tin thuốc -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Thông tin thuốc</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-4">
                                  <label class="form-label">Số đăng ký <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" name="so_dang_ky" id="medicine_edit_so_dang_ky" placeholder="Bắt buộc" required>
                              </div>
                              <div class="col-md-4">
                                  <label class="form-label">Hoạt chất <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" name="hoat_chat" id="medicine_edit_hoat_chat" placeholder="Bắt buộc" required>
                              </div>
                              <div class="col-md-4">
                                  <label class="form-label">Hàm lượng <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" name="ham_luong" id="medicine_edit_ham_luong" placeholder="Bắt buộc" required>
                              </div>
                              <div class="col-md-4">
                                  <label class="form-label">
                                      Đường dùng <span class="text-danger">*</span>
                                  </label>
                                  <div class="position-relative">
                                      <select class="form-select" name="drugusage_id" id="medicine_edit_duong_dung_select" required onchange="handleEditDrugRouteChange(this)">
                                          <option value="">Bắt buộc</option>
                                          @foreach($drugRoutes as $usage)
                                              <option value="{{ $usage->id }}">{{ $usage->name }}</option>
                                          @endforeach
                                          <option value="create_new">+ Tạo mới đường dùng</option>
                                      </select>
                                      
                                      <!-- Inline form cho Drug Route -->
                                      <div id="editDrugRouteInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                          <div class="mb-2">
                                              <input type="text" class="form-control" id="editNewDrugRouteName" placeholder="Nhập tên đường dùng mới">
                                          </div>
                                          <div class="d-flex gap-2">
                                              <button type="button" class="btn btn-success" onclick="createNewEditDrugRouteInline()">
                                                  <i class="fas fa-save"></i> Lưu
                                              </button>
                                              <button type="button" class="btn btn-secondary" onclick="cancelEditDrugRouteForm()">
                                                  <i class="fas fa-times"></i> Hủy
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>   
                              <div class="col-md-4">
                                  <label class="form-label">Hãng sản xuất<span class="text-danger">*</span></label>
                                  <div class="position-relative">
                                      <select class="form-select" name="manufacturer_id" id="medicine_edit_manufacturer_select" required onchange="handleEditManufacturerChange(this)">
                                          <option value="">Tìm hãng sản xuất</option>
                                          @foreach($manufacturers as $manu)
                                              <option value="{{ $manu->id }}">{{ $manu->name }}</option>
                                          @endforeach
                                          <option value="create_new">+ Tạo mới hãng sản xuất</option>
                                      </select>
                                      
                                      <!-- Inline form cho Manufacturer - EDIT MEDICINE -->
                                      <div id="editMedicineManufacturerInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                          <div class="mb-2">
                                              <input type="text" class="form-control" id="editNewMedicineManufacturerName" placeholder="Nhập tên hãng sản xuất mới">
                                          </div>
                                          <div class="d-flex gap-2">
                                              <button type="button" class="btn btn-success" onclick="createNewEditManufacturerInline()">
                                                  <i class="fas fa-save"></i> Lưu
                                              </button>
                                              <button type="button" class="btn btn-secondary" onclick="cancelEditManufacturerForm()">
                                                  <i class="fas fa-times"></i> Hủy
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>                  
                              <div class="col-md-4">
                                  <label class="form-label">Nước sản xuất</label>
                                  <input type="text" class="form-control" name="nuoc_san_xuat" id="medicine_edit_nuoc_san_xuat" placeholder="Tìm nước sản xuất">
                              </div>
                          </div>
                          <div class="row g-3 mb-2">
                              <div class="col-md-3">
                                  <label class="form-label">Quy cách đóng gói <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" name="quy_cach_dong_goi" id="medicine_edit_quy_cach_dong_goi" placeholder="Bắt buộc" required>
                              </div>
                          </div>
                      </fieldset>

                      <!-- Tồn kho -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Tồn kho</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-6">
                                  <label class="form-label">Định mức tồn thấp nhất</label>
                                  <input type="number" class="form-control" name="ton_thap_nhat" id="medicine_edit_ton_thap_nhat" value="0">
                              </div>
                              <div class="col-md-6">
                                  <label class="form-label">Định mức tồn cao nhất</label>
                                  <input type="number" class="form-control" name="ton_cao_nhat" id="medicine_edit_ton_cao_nhat" value="999999999">
                              </div>
                          </div>
                      </fieldset>

                      <!-- Vị trí, trọng lượng -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Vị trí, trọng lượng</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-4">
                                  <label class="form-label">Vị trí</label>
                                  <div class="position-relative">
                                      <select class="form-select" name="position_id" id="medicine_edit_position_select" onchange="handleEditPositionChange(this)">
                                          <option value="">Chọn vị trí</option>
                                          @foreach($positions as $pos)
                                              <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                          @endforeach
                                          <option value="create_new">+ Tạo mới vị trí</option>
                                      </select>
                                      
                                      <!-- Inline form cho Position -->
                                      <div id="editMedicinePositionInlineForm" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                          <div class="mb-2">
                                              <input type="text" class="form-control" id="editNewMedicinePositionName" placeholder="Nhập tên vị trí mới">
                                          </div>
                                          <div class="d-flex gap-2">
                                              <button type="button" class="btn btn-success" onclick="createNewEditPositionInline()">
                                                  <i class="fas fa-save"></i> Lưu
                                              </button>
                                              <button type="button" class="btn btn-secondary" onclick="cancelEditPositionForm()">
                                                  <i class="fas fa-times"></i> Hủy
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-4">
                                  <label class="form-label">Trọng lượng</label>
                                  <div class="input-group">
                                      <input type="number" class="form-control" name="trong_luong" id="medicine_edit_trong_luong" value="0">
                                      <span class="input-group-text">g</span>
                                  </div>
                              </div>
                          </div>
                      </fieldset>

                      <!-- Thiết lập đơn vị tính -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Thiết lập đơn vị tính</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-8">
                                  <input type="text" class="form-control" name="don_vi_tinh" id="medicine_edit_don_vi_tinh_input" placeholder="Nhập đơn vị tính" readonly>
                              </div>
                              <div class="col-md-4 d-flex align-items-end">
                                  <button type="button" class="btn btn-outline-primary w-100" onclick="openEditUnitModal()">Thiết lập</button>
                              </div>
                          </div>
                      </fieldset>

                      <!-- Bán trực tiếp -->
                      <div class="form-check mb-3">
                          <input class="form-check-input" type="checkbox" id="medicine_edit_ban_truc_tiep" name="ban_truc_tiep">
                          <label class="form-check-label" for="medicine_edit_ban_truc_tiep">
                              Bán trực tiếp
                          </label>
                      </div>          
                  </div>
                  
                  <!-- Tab Mô tả -->
                  <div class="tab-pane fade" id="edit-desc" role="tabpanel">
                      <div class="mb-3">
                          <label for="mo_ta" class="form-label">Mô tả sản phẩm</label>
                          <textarea name="mo_ta" data-rte="true" data-rte-height="300"></textarea>
                      </div>
                  </div>
              </div>
          </div>
          
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Lưu thay đổi
              </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Include Modal Components -->
  @include('admin.products.Danhsachhanghoa.formmodal.unit_modal')
  
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
  @endpush
  
  @push('scripts')
  <script src="{{ asset('js/forms.js') }}"></script>
  @endpush  