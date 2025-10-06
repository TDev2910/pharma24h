<div class="modal fade create-modal" id="createGoodsModal" tabindex="-1" aria-labelledby="createGoodsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header">
          <h5 class="modal-title" id="createGoodsModalLabel">
            <i style="margin-right:10px"></i>Tạo hàng hóa 
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('admin.products.storeGoods') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body p-4">
              <!-- Tabs -->
              <ul class="nav nav-tabs border-0 mb-3" id="createGoodsTab" role="tablist">
                  <li class="" role="presentation">
                      <button class="nav-link active px-4 py-2 fw-bold" id="create-goods-info-tab" data-bs-toggle="tab" data-bs-target="#create-goods-info" type="button" role="tab" style="margin-left:1px;margin-bottom:-2px">Thông tin</button>
                  </li>
                  <li class="" role="presentation">
                      <button class="nav-link px-4 py-2 fw-bold" id="create-goods-desc-tab" data-bs-toggle="tab" data-bs-target="#create-goods-desc" type="button" role="tab" style="border-radius:8px;">Mô tả</button>
                  </li>
              </ul>
              <hr class="border-0 border-top border-secondary-subtle" style="background-color: black; height: 2px;margin-top:-14px">
              <div class="tab-content" id="createGoodsTabContent">
                  <!-- Tab Thông tin -->
                  <div class="tab-pane fade show active" id="create-goods-info" role="tabpanel">
                      <!-- THÔNG TIN CƠ BẢN -->
                      <div class="row mb-4">
                          <!-- Inputs bên trái -->
                          <div class="col-md-8">
                              <div class="row g-2">
                                  <div class="col-md-6">
                                      <label class="form-label">Mã hàng</label>
                                      <div class="input-group">
                                          <input type="text" class="form-control text-muted" name="ma_hang" id="goods_ma_hang" placeholder="Tự động" readonly>
                                          <button class="btn btn-outline-secondary" type="button" id="generateGoodsCodeBtn">
                                              <i class="fas fa-sync-alt"></i> Tạo mã
                                          </button>
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Mã vạch</label>
                                      <div class="input-group">
                                          <input type="text" class="form-control text-muted" name="ma_vach" id="goods_ma_vach" placeholder="Tự động" readonly>
                                          <button class="btn btn-outline-secondary" type="button" id="generateGoodsBarcodeBtn">
                                              <i class="fas fa-sync-alt"></i> Tạo mã
                                          </button>
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                      <label class="form-label">Tên hàng hóa <span class="text-danger">*</span></label>
                                      <input type="text" class="form-control" name="ten_hang_hoa" id="ten_hang_hoa" placeholder="Nhập tên hàng hóa" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Tên viết tắt</label>
                                      <input type="text" class="form-control" name="ten_viet_tat" id="ten_viet_tat" placeholder="Nhập tên viết tắt (tùy chọn)">
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Nhóm hàng <span class="text-danger">*</span></label>
                                      <select class="form-select" name="nhom_hang_id" id="nhom_hang_id" required>
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
                              <!-- Chỉ 1 ảnh duy nhất - CÓ PREVIEW -->
                              <div class="border-2 border-dashed border-primary rounded-3 d-flex flex-column align-items-center justify-content-center position-relative bg-light" 
                                   style="width:200px;height:200px;background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="create-goods-image-preview-container">
                                  <input type="file" name="image" accept="image/*" 
                                      style="opacity:0;position:absolute;width:100%;height:100%;cursor:pointer;top:0;left:0;z-index:1;"
                                      onchange="previewCreateGoodsImage(this)">
                                  <div class="text-center" id="create-goods-image-placeholder"> 
                                      <i class="fas fa-image fa-3x text-primary mb-3"></i>
                                      <div class="fw-bold text-primary mb-2">Thêm ảnh sản phẩm</div>
                                      <small class="text-muted">Click để chọn ảnh</small>
                                      <div class="mt-3">
                                          <span class="badge bg-light text-dark">Tối đa 2MB</span>
                                      </div>
                                  </div>
                                  <img id="create-goods-image-preview" src="" alt="Preview" 
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
                                  <input type="text" class="form-control" name="gia_von" id="gia_von" value="0" required>
                              </div>
                              <div class="col-md-6">
                                  <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                  <div class="input-group">
                                      <input type="text" class="form-control" name="gia_ban" id="gia_ban" value="0" required>
                                      <button class="btn btn-outline-secondary" type="button">Thiết lập giá</button>
                                  </div>
                              </div>
                          </div>
                      </fieldset>
  
                      <!-- Thông tin hàng hóa -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Thông tin hàng hóa</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-5">
                                  <label class="form-label">Quy cách đóng gói <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" name="quy_cach_dong_goi" id="quy_cach_dong_goi" placeholder="Bắt buộc" required>
                              </div>
                              <div class="col-md-6">
                                  <label class="form-label">Hãng sản xuất</label>
                                  <div class="input-group">
                                      <select class="form-select" name="manufacturer_id" id="goods_manufacturer_select">
                                          <option value="">Tìm hãng sản xuất</option>
                                          @foreach($manufacturers as $manu)
                                              <option value="{{ $manu->id }}">{{ $manu->name }}</option>
                                          @endforeach
                                      </select>
                                      <button class="btn btn-outline-secondary" type="button" id="btnManageGoodsManufacturer">
                                          <i class="fas fa-cog"></i> Quản lý
                                      </button>
                                  </div>
                                  <div class="text-muted mt-1" style="font-size:12px">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</div>
                              </div>
                              <div class="col-md-4">
                                  <label class="form-label">Nước sản xuất</label>
                                  <input type="text" class="form-control" name="nuoc_san_xuat" id="nuoc_san_xuat" placeholder="Tìm nước sản xuất">
                              </div>
                          </div>
                      </fieldset>
  
                      <!-- Tồn kho -->
                      <fieldset class="mb-4 border rounded p-3">
                        <legend class="float-none w-auto px-2 fs-6">Tồn kho</legend>
                        <div class="row g-3 mb-2">
                            <div class="col-md-4">
                                <label class="form-label">Tồn kho</label>
                                <input type="number" class="form-control" name="ton_kho" id="create_goods_ton_kho" value="0" readonly>
                                <small class="text-muted">Số lượng hiện có trong kho (mặc định: 0)</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Định mức tồn thấp nhất</label>
                                <input type="number" class="form-control" name="ton_thap_nhat" id="create_goods_ton_thap_nhat" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Định mức tồn cao nhất</label>
                                <input type="number" class="form-control" name="ton_cao_nhat" id="create_goods_ton_cao_nhat" value="999999999">
                            </div>
                        </div>
                    </fieldset>
  
                      <!-- Vị trí, trọng lượng -->
                      <fieldset class="mb-4 border rounded p-3">
                          <legend class="float-none w-auto px-2 fs-6">Vị trí, trọng lượng</legend>
                          <div class="row g-3 mb-2">
                              <div class="col-md-6">
                                  <label class="form-label">
                                      Vị trí <span class="text-danger">*</span>
                                  </label>
                                  <div class="input-group">
                                      <select class="form-select" name="position_id" id="goods_position_select">
                                          <option value="">Chọn vị trí</option>
                                          @foreach($positions as $pos)
                                              <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                          @endforeach
                                      </select>
                                      <button class="btn btn-outline-secondary" type="button" id="btnManageGoodsPosition">
                                          <i class="fas fa-cog"></i> Quản lý
                                      </button>
                                  </div>
                                  <div class="text-muted mt-1" style="font-size:12px">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</div>
                              </div>
                              <div class="col-md-4">
                                  <label class="form-label">Trọng lượng</label>
                                  <div class="input-group">
                                      <input type="number" class="form-control" name="trong_luong" id="trong_luong" value="0">
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
                                <input type="text" class="form-control" name="don_vi_tinh" id="don_vi_tinh_input" placeholder="Nhập đơn vị tính" readonly>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="openCreateUnitModal()">Thiết lập</button>
                            </div>
                        </div>
                    </fieldset> 
  
                      <!-- Bán trực tiếp -->
                      <div class="form-check mb-3">
                          <input class="form-check-input" type="checkbox" id="ban_truc_tiep" name="ban_truc_tiep">
                          <label class="form-check-label" for="ban_truc_tiep">
                              Bán trực tiếp
                          </label>
                      </div>          
                  </div>
                  
                  <!-- Tab Mô tả -->
                  <div class="tab-pane fade" id="create-goods-desc" role="tabpanel">
                    <div class="mb-3">
                      <label for="mo_ta" class="form-label">Mô tả sản phẩm</label>
                      <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5"
                                placeholder="Nhập mô tả chi tiết về sản phẩm..."
                                data-rte="true" data-rte-height="300"></textarea>
                    </div>
                  </div>
              </div>
          </div>
          
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-success">
                  <i></i> Lưu hàng hóa
              </button>
          </div>
        </form>
      </div>
    </div>
  </div>

<!-- Modal Quản lý Hãng sản xuất cho Goods -->
<div class="modal fade" id="manageGoodsManufacturerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Quản lý hãng sản xuất</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col">
                        <input type="text" id="manageGoodsManufacturerSearch" class="form-control" placeholder="Tìm theo tên…">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" id="manageGoodsManufacturerAddBtn">
                            <i class="fas fa-plus"></i> Thêm
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width:90px">Mã</th>
                                <th>Tên hãng sản xuất</th>
                                <th class="text-end" style="width:160px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="manageGoodsManufacturerTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quản lý Vị trí cho Goods -->
<div class="modal fade" id="manageGoodsPositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Quản lý vị trí</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col">
                        <input type="text" id="manageGoodsPositionSearch" class="form-control" placeholder="Tìm theo tên…">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" id="manageGoodsPositionAddBtn">
                            <i class="fas fa-plus"></i> Thêm
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width:90px">Mã</th>
                                <th>Tên vị trí</th>
                                <th class="text-end" style="width:160px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="manageGoodsPositionTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/create-modal.css') }}">
@endpush

@push('scripts')
<script>
    //hãng sản xuất 
    let goodsManufacturers = @json($manufacturers);
    const selectGoodsManufacturerEl = document.getElementById('goods_manufacturer_select');
    const btnManageGoodsManufacturer = document.getElementById('btnManageGoodsManufacturer');
    const manageGoodsManufacturerModal = new bootstrap.Modal(document.getElementById('manageGoodsManufacturerModal'));
    const manageGoodsManufacturerTbody = document.getElementById('manageGoodsManufacturerTbody');
    const manageGoodsManufacturerSearch = document.getElementById('manageGoodsManufacturerSearch');
    const manageGoodsManufacturerAddBtn = document.getElementById('manageGoodsManufacturerAddBtn');

    function nextGoodsManufacturerId() 
    {
        return goodsManufacturers.length ? Math.max(...goodsManufacturers.map(m => m.id)) + 1 : 1;
    }

    function syncGoodsManufacturerSelect(selectedId = '')
    {
        selectGoodsManufacturerEl.innerHTML = '';
        selectGoodsManufacturerEl.appendChild(new Option('Tìm hãng sản xuất', '', true, !selectedId));
        goodsManufacturers.forEach(m => selectGoodsManufacturerEl.appendChild(new Option(m.name, m.id, false, m.id == selectedId)));
    }

    function renderGoodsManufacturerManageTable(filter = '') {
        const q = (filter || '').toLowerCase().trim();
        const items = goodsManufacturers.filter(m => !q || m.name.toLowerCase().includes(q));
        manageGoodsManufacturerTbody.innerHTML = items.map(m => `
            <tr data-id="${m.id}">
                <td><span class="badge text-bg-secondary">#${m.id}</span></td>
                <td class="name-cell">${m.name}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-edit"><i class="fas fa-edit"></i> Sửa</button>
                        <button class="btn btn-outline-danger btn-del"><i class="fas fa-trash"></i> Xóa</button>
                    </div>
                </td>
            </tr>`).join('') || `<tr><td colspan="3" class="text-center text-muted py-4">Không có mục nào.</td></tr>`;
    }

    function enterGoodsManufacturerEditMode(row) {
        const id = +row.dataset.id;
        const item = goodsManufacturers.find(m => m.id === id);
        const cell = row.querySelector('.name-cell');
        cell.innerHTML = `
            <div class="d-flex gap-2">
                <input class="form-control form-control-sm edit-input" value="${item.name}">
                <button class="btn btn-success btn-sm btn-save"><i class="fas fa-check"></i></button>
                <button class="btn btn-light border btn-sm btn-cancel">Hủy</button>
            </div>`;
        
        cell.querySelector('.btn-save').addEventListener('click', () => {
            const val = cell.querySelector('.edit-input').value.trim();
            if (!val) return;
            if (goodsManufacturers.some(m => m.id !== id && m.name.toLowerCase() === val.toLowerCase())) {
                alert('Hãng sản xuất này đã tồn tại.'); 
                return;
            }
            
            // Nếu là item mới (có tên placeholder), gọi API tạo mới
            if (item.name === 'Nhập hãng sản xuất mới') {
                fetch('{{ route("admin.products.manufacturer.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật với ID thật từ server
                        const oldId = item.id; // Lưu lại ID tạm thời
                        item.id = data.manufacturer.id;
                        item.name = data.manufacturer.name;
                        
                        // Cập nhật lại mảng manufacturers với ID mới
                        goodsManufacturers = goodsManufacturers.map(m => m.id === oldId ? item : m);
                        
                        console.log('Manufacturer created, old ID: ' + oldId + ', new ID: ' + item.id);
                        
                        renderGoodsManufacturerManageTable(manageGoodsManufacturerSearch.value);
                        syncGoodsManufacturerSelect(data.manufacturer.id);
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tạo hãng sản xuất!');
                });
            } else {
                // Nếu là item đã tồn tại, gọi API update
                fetch('{{ route("admin.products.manufacturer.update", ":id") }}'.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        item.name = val;
                        renderGoodsManufacturerManageTable(manageGoodsManufacturerSearch.value);
                        syncGoodsManufacturerSelect(+selectGoodsManufacturerEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật hãng sản xuất!');
                });
            }
        });
        
        cell.querySelector('.btn-cancel').addEventListener('click', () => {
            // Nếu là item mới (có tên placeholder), xóa khỏi array
            if (item.name === 'Nhập hãng sản xuất mới') {
                goodsManufacturers = goodsManufacturers.filter(m => m.id !== id);
            }
            renderGoodsManufacturerManageTable(manageGoodsManufacturerSearch.value);
        });
    }

    btnManageGoodsManufacturer.addEventListener('click', () => {
        manageGoodsManufacturerSearch.value = '';
        renderGoodsManufacturerManageTable();
        manageGoodsManufacturerModal.show();
    });

    manageGoodsManufacturerSearch.addEventListener('input', e => renderGoodsManufacturerManageTable(e.target.value));

    //js xử lý tạo mới hãng sản xuất
    manageGoodsManufacturerAddBtn.addEventListener('click', () => {
        // Tạo item mới với tên placeholder
        const id = nextGoodsManufacturerId();
        goodsManufacturers.push({ id, name: 'Nhập hãng sản xuất mới' });
        renderGoodsManufacturerManageTable(manageGoodsManufacturerSearch.value);
        // Tìm row vừa tạo và tự động vào edit mode
        const row = [...manageGoodsManufacturerTbody.querySelectorAll('tr')].find(tr => +tr.dataset.id === id);
        if (row) {
            enterGoodsManufacturerEditMode(row);
            // Focus vào input field và select text
            setTimeout(() => {
                const input = row.querySelector('.edit-input');
                if (input) {
                    input.focus();
                    input.select();
                }
            }, 100);
        }
    });

    manageGoodsManufacturerTbody.addEventListener('click', e => {
        const row = e.target.closest('tr'); 
        if (!row) return;
        
        if (e.target.closest('.btn-edit')) {
            enterGoodsManufacturerEditMode(row);
        }
        
        if (e.target.closest('.btn-del')) {
            const id = +row.dataset.id;
            const item = goodsManufacturers.find(m => m.id === id);
            if (confirm(`Xóa "${item.name}"?`)) {
                // Gọi API để xóa khỏi database
                fetch('{{ route("admin.products.manufacturer.destroy", ":id") }}'.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa khỏi array local
                        goodsManufacturers = goodsManufacturers.filter(m => m.id !== id);
                        renderGoodsManufacturerManageTable(manageGoodsManufacturerSearch.value);
                        if (+selectGoodsManufacturerEl.value === id) selectGoodsManufacturerEl.value = '';
                        syncGoodsManufacturerSelect(+selectGoodsManufacturerEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa hãng sản xuất!');
                });
            }
        }
    });
    syncGoodsManufacturerSelect('');

    //vị trí
    let goodsPositions = @json($positions);
    const selectGoodsPositionEl = document.getElementById('goods_position_select');
    const btnManageGoodsPosition = document.getElementById('btnManageGoodsPosition');
    const manageGoodsPositionModal = new bootstrap.Modal(document.getElementById('manageGoodsPositionModal'));
    const manageGoodsPositionTbody = document.getElementById('manageGoodsPositionTbody');
    const manageGoodsPositionSearch = document.getElementById('manageGoodsPositionSearch');
    const manageGoodsPositionAddBtn = document.getElementById('manageGoodsPositionAddBtn');

    function nextGoodsPositionId()
    {
        return goodsPositions.length ? Math.max(...goodsPositions.map(p => p.id)) + 1 : 1;
    }

    function syncGoodsPositionSelect(selectedId = '')
    {
        selectGoodsPositionEl.innerHTML = '';
        selectGoodsPositionEl.appendChild(new Option('Chọn vị trí', '', true, !selectedId));
        goodsPositions.forEach(p => selectGoodsPositionEl.appendChild(new Option(p.name, p.id, false, p.id == selectedId)));
    }

    function renderGoodsPositionManageTable(filter = '')
    {
        const q = (filter || '').toLowerCase().trim();
        const items = goodsPositions.filter(r => !q || r.name.toLowerCase().includes(q));
        manageGoodsPositionTbody.innerHTML = items.map(r => `
            <tr data-id="${r.id}">
                <td><span class="badge text-bg-secondary">#${r.id}</span></td>
                <td class="name-cell">${r.name}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-edit"><i class="fas fa-edit"></i> Sửa</button>
                        <button class="btn btn-outline-danger btn-del"><i class="fas fa-trash"></i> Xóa</button>
                    </div>
                </td>
        </tr>`).join('') || `<tr><td colspan="3" class="text-center text-muted py-4">Không có mục nào.</td></tr>`;
    }

    function enterGoodsPositionEditMode(row) {
        const id = +row.dataset.id;
        const item = goodsPositions.find(r => r.id === id);
        const cell = row.querySelector('.name-cell');
        cell.innerHTML = `
            <div class="d-flex gap-2">
                <input class="form-control form-control-sm edit-input" value="${item.name}">
                <button class="btn btn-success btn-sm btn-save"><i class="fas fa-check"></i></button>
                <button class="btn btn-light border btn-sm btn-cancel">Hủy</button>
            </div>`;
        
        cell.querySelector('.btn-save').addEventListener('click', () => {
            const val = cell.querySelector('.edit-input').value.trim();
            if(!val) return;
            if(goodsPositions.some(r => r.id !== id && r.name.toLowerCase() === val.toLowerCase()))
            {
                alert('Vị trí đã tồn tại');
                return;
            }
            //nếu là item mới (có tên placeholder), gọi api tạo mới
            if(item.name === 'Nhập vị trí mới')
            {
                fetch('{{ route("admin.products.position.store") }}', {
                    method:'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data=> {
                    if(data.success) 
                    {
                        //cập nhật với id gửi từ server
                        const oldId = item.id; // Lưu lại ID tạm thời
                        item.id = data.position.id; // Cập nhật ID từ server
                        item.name = data.position.name;
                        
                        // Cập nhật lại mảng positions với ID mới
                        goodsPositions = goodsPositions.map(p => p.id === oldId ? item : p);
                        
                        console.log('Position created, old ID: ' + oldId + ', new ID: ' + item.id);
                        
                        renderGoodsPositionManageTable(manageGoodsPositionSearch.value);
                        syncPositionSelect(data.position.id);
                    }
                    else
                    {
                        console.error('Error:', data.message);
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Có lỗi xảy ra khi tạo vị trí!');
                });
            } else {
                // Gọi API để update trong database
                fetch('{{ route("admin.products.position.update", ":id") }}'.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: val })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        item.name = val;
                        renderGoodsPositionManageTable(manageGoodsPositionSearch.value);
                        syncGoodsPositionSelect(+selectGoodsPositionEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật vị trí!');
                });
            }
        });

        cell.querySelector('.btn-cancel').addEventListener('click', () => {
            if(item.name === 'Nhập vị trí mới')
            {
                goodsPositions = goodsPositions.filter(r => r.id !== id);
            }
            renderGoodsPositionManageTable(manageGoodsPositionSearch.value);
        });  
    }
    btnManageGoodsPosition.addEventListener('click', () => {
        manageGoodsPositionSearch.value = '';
        renderGoodsPositionManageTable();
        manageGoodsPositionModal.show();
    });

    manageGoodsPositionSearch.addEventListener('input', e => renderGoodsPositionManageTable(e.target.value));

    manageGoodsPositionAddBtn.addEventListener('click', () => {
        const id = nextGoodsPositionId();
        goodsPositions.push({id,name:'Nhập vị trí mới'});
        renderGoodsPositionManageTable(manageGoodsPositionSearch.value);
        //tìm row vừa tạo và tự động vào edit
        const row = [...manageGoodsPositionTbody.querySelectorAll('tr')].find(tr => +tr.dataset.id === id);
        if(row)
        {
            enterGoodsPositionEditMode(row);
            setTimeout(() => {
                const input = row.querySelector('.edit-input');
                if(input)
                {
                    input.focus();
                    input.select();
                }
            },100);
        }
    });

    manageGoodsPositionTbody.addEventListener('click', e => {
        const row = e.target.closest('tr'); 
        if (!row) return;
        
        if (e.target.closest('.btn-edit')) {
            enterGoodsPositionEditMode(row);
        }
        
        if (e.target.closest('.btn-del')) {
            const id = +row.dataset.id;
            const item = goodsPositions.find(r => r.id === id);
            if (confirm(`Xóa "${item.name}"?`)) {
                // Gọi API để xóa khỏi database
                fetch('{{ route("admin.products.position.destroy", ":id") }}'.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa khỏi array local
                        goodsPositions = goodsPositions.filter(r => r.id !== id);
                        renderGoodsPositionManageTable(manageGoodsPositionSearch.value);
                        if (+selectGoodsPositionEl.value === id) selectGoodsPositionEl.value = '';
                        syncGoodsPositionSelect(+selectGoodsPositionEl.value || '');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa vị trí!');
                });
            }
        }   
    });

    syncGoodsPositionSelect('');

    // Tạo mã hàng và mã vạch ngẫu nhiên
    document.getElementById('generateGoodsCodeBtn').addEventListener('click', function() {
        fetch('/admin/goods/generate-codes', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('goods_ma_hang').value = data.ma_hang;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi khi tạo mã hàng!');
        });
    });

    document.getElementById('generateGoodsBarcodeBtn').addEventListener('click', function() {
        fetch('/admin/goods/generate-codes', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('goods_ma_vach').value = data.ma_vach;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi khi tạo mã vạch!');
        });
    });

</script>
@endpush

<style>
/* Modal nhỏ gọn nhưng đủ rộng cho Goods */
#createGoodsModal .modal-dialog {
  max-width: 850px;
}

#createGoodsModal .modal-content {
  border-radius: 8px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

#createGoodsModal .modal-header {
  padding: 15px 20px;
  background: #f8f9fa;
  border-bottom: 1px solid #ebedf0;
}

#createGoodsModal .modal-title {
  font-size: 16px;
  font-weight: 600;
}

#createGoodsModal .modal-body {
  padding: 20px;
}

/* Input nhỏ gọn nhưng dễ thao tác */
#createGoodsModal .form-control {
  font-size: 14px;
  padding: 8px 12px;
  height: 38px;
  border-radius: 6px;
}

#createGoodsModal .form-select {
  font-size: 14px;
  padding: 8px 28px 8px 12px;
  height: 38px;
  border-radius: 6px;
}

/* Label rõ ràng hơn */
#createGoodsModal label {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 5px;
  color: #495057;
}

/* Button nhỏ gọn */
#createGoodsModal .btn {
  font-size: 14px;
  padding: 6px 12px;
  border-radius: 6px;
}

/* Cải thiện spacing cho form groups */
#createGoodsModal .form-group {
  margin-bottom: 15px;
}

/* Footer */
#createGoodsModal .modal-footer {
  padding: 15px 20px;
  border-top: 1px solid #ebedf0;
}

/* Input group cho hãng sản xuất và vị trí */
#createGoodsModal .input-with-button {
  display: flex;
}

#createGoodsModal .input-with-button select {
  flex: 1;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

#createGoodsModal .input-with-button .btn {
  width: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}
</style>
