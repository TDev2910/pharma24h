<template>
  <div class="tab-content">
    <!-- THÔNG TIN CƠ BẢN -->
    <div class="form-section">
      <div class="form-row">
        <!-- Left inputs -->
        <div class="form-column">
          <div class="form-row">
            <div class="form-field">
              <label class="field-label">Mã hàng</label>
              <div class="input-group">
                <InputText v-model="form.ma_hang" placeholder="Tự động" readonly class="field-input readonly-input" />
                <Button type="button" icon="pi pi-refresh" label="Tạo mã" severity="secondary" size="small" @click="$emit('generate-code')" />
              </div>
            </div>

            <div class="form-field">
              <label class="field-label">Mã vạch</label>
              <div class="input-group">
                <InputText v-model="form.ma_vach" placeholder="Tự động" readonly class="field-input readonly-input" />
                <Button type="button" icon="pi pi-refresh" label="Tạo mã" severity="secondary" size="small" @click="$emit('generate-barcode')" />
              </div>
            </div>
          </div>

          <div class="form-field full-width">
            <label class="field-label">Tên hàng hóa <span class="text-danger">*</span></label>
            <InputText v-model="form.ten_hang_hoa" placeholder="Nhập tên hàng hóa" class="field-input" :class="{ 'p-invalid': form.errors.ten_hang_hoa }" />
            <small v-if="form.errors.ten_hang_hoa" class="p-error">{{ form.errors.ten_hang_hoa }}</small>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="field-label">Tên viết tắt</label>
              <InputText v-model="form.ten_viet_tat" placeholder="Nhập tên viết tắt" class="field-input" style="width: 240px;" />
            </div>

            <div class="form-field">
              <label class="field-label">Nhóm hàng <span class="text-danger">*</span></label>
              <TreeSelect 
                :modelValue="selectedCategoryKey" 
                @update:modelValue="val => $emit('category-change', { value: val })" 
                :options="categoryTreeNodes" 
                placeholder="Chọn nhóm hàng"
                class="modern-treeselect w-full" 
                :class="{ 'p-invalid': form.errors.nhom_hang_id }"
                selectionMode="single" 
              />
              <small v-if="form.errors.nhom_hang_id" class="p-error">{{ form.errors.nhom_hang_id }}</small>
            </div>
          </div>
        </div>
        
        <!-- Right image upload via Slot -->
        <slot name="image"></slot>
      </div>
    </div>

    <!-- Giá vốn, giá bán -->
    <fieldset class="mb-4 border rounded p-3">
      <legend class="float-none w-auto px-2 fs-6">Giá vốn, giá bán</legend>
      <div class="form-row">
        <div class="form-field">
          <label class="field-label">Giá vốn <span class="text-danger">*</span></label>
          <InputNumber v-model="form.gia_von" mode="currency" currency="VND" locale="vi-VN" class="price-input" :class="{ 'p-invalid': form.errors.gia_von }" />
          <small v-if="form.errors.gia_von" class="p-error">{{ form.errors.gia_von }}</small>
        </div>

        <div class="form-field">
          <label class="field-label">Giá bán <span class="text-danger">*</span></label>
          <div class="input-group">
            <InputNumber v-model="form.gia_ban" mode="currency" currency="VND" locale="vi-VN" class="price-input" :class="{ 'p-invalid': form.errors.gia_ban }" />
          </div>
          <small v-if="form.errors.gia_ban" class="p-error">{{ form.errors.gia_ban }}</small>
        </div>
      </div>
    </fieldset>

    <!-- Thông tin chung -->
    <div class="form-section" style="margin-top: 20px;">
      <div class="section-header">
        <span class="section-title">Thông tin chung</span>
      </div>
      
      <div class="form-row">
        <div class="form-field">
          <label class="field-label">Hãng sản xuất <span class="text-danger">*</span></label>
          <div class="input-group">
            <Dropdown v-model="form.manufacturer_id" :options="manufacturerOptions" optionLabel="name" optionValue="id" placeholder="Tìm hãng sản xuất" class="field-input" :class="{ 'p-invalid': form.errors.manufacturer_id }" />
            <Button type="button" icon="pi pi-cog" @click="$emit('open-manufacturer-modal')" severity="secondary" size="small" title="Quản lý" />
          </div>
          <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
          <small v-if="form.errors.manufacturer_id" class="p-error">{{ form.errors.manufacturer_id }}</small>
        </div>

        <div class="form-field">
          <label class="field-label">Quy cách đóng gói <span class="text-danger">*</span></label>
          <InputText v-model="form.quy_cach_dong_goi" placeholder="Bắt buộc" class="field-input" :class="{ 'p-invalid': form.errors.quy_cach_dong_goi }" />
          <small v-if="form.errors.quy_cach_dong_goi" class="p-error">{{ form.errors.quy_cach_dong_goi }}</small>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label class="field-label">Nước sản xuất</label>
          <InputText v-model="form.nuoc_san_xuat" placeholder="Tìm nước sản xuất" class="field-input" />
        </div>
      </div>
    </div>

    <!-- Tồn kho -->
    <fieldset class="mb-4 border rounded p-3">
      <legend class="float-none w-auto px-2 fs-6">Tồn kho</legend>
      <div class="form-row three-columns">
        <div class="form-field">
          <label class="field-label">Tồn kho</label>
          <InputText v-model="form.ton_kho" placeholder="0" readonly class="field-input readonly-input" />
          <small class="text-muted">Số lượng hiện có trong kho</small>
        </div>

        <div class="form-field">
          <label class="field-label">Định mức tồn thấp nhất</label>
          <InputText v-model="form.ton_thap_nhat" placeholder="Nhập số lượng" class="field-input" />
          <small class="text-muted">Cảnh báo khi ≤ số này</small>
        </div>

        <div class="form-field">
          <label class="field-label">Định mức tồn cao nhất</label>
          <InputText v-model="form.ton_cao_nhat" placeholder="Nhập số lượng" class="field-input" />
          <small class="text-muted">Cảnh báo khi ≥ số này</small>
        </div>
      </div>
    </fieldset>

    <!-- Vị trí, trọng lượng -->
    <div class="form-section" style="margin-top: 20px;">
      <div class="section-header">
        <span class="section-title">Vị trí, trọng lượng</span>
      </div>
      <div class="form-row">
        <div class="form-field">
          <label class="field-label">Vị trí <span class="text-danger">*</span></label>
          <div class="input-group">
            <Dropdown v-model="form.position_id" :options="positionOptions" optionLabel="name" optionValue="id" placeholder="Chọn vị trí" class="field-input" :class="{ 'p-invalid': form.errors.position_id }" />
            <Button type="button" icon="pi pi-cog" @click="$emit('open-position-modal')" severity="secondary" size="small" title="Quản lý" />
          </div>
          <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
          <small v-if="form.errors.position_id" class="p-error">{{ form.errors.position_id }}</small>
        </div>

        <div class="form-field">
          <label class="field-label">Trọng lượng</label>
          <div style="display: flex; align-items: center; max-width: 350px;">
            <InputText v-model="form.trong_luong" :min="0" class="field-input" style="flex: 1 1 0; border-top-right-radius: 0; border-bottom-right-radius: 0;" />
            <span class="input-group-text" style="background: #f6f7f9; border: 1px solid #ced4da; border-left: none; border-radius: 0 6px 6px 0; padding: 0 14px; font-size: 14px; height: 38px; display: flex; align-items: center;">g</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Thiết lập đơn vị tính -->
    <div class="form-section">
      <div class="section-header">
        <span class="section-title">Thiết lập đơn vị tính</span>
      </div>
      <div class="form-row">
        <div class="form-field">
          <InputText v-model="form.don_vi_tinh" placeholder="Nhập đơn vị tính" readonly class="field-input readonly-input" />
        </div>
        <div class="form-field">
          <Button type="button" label="Thiết lập" @click="$emit('open-unit-modal')" severity="secondary" class="w-full" />
        </div>
      </div>
    </div>

    <!-- Bán trực tiếp -->
    <div class="form-section">
      <div class="checkbox-field">
        <Checkbox v-model="form.ban_truc_tiep" :binary="true" inputId="ban_truc_tiep" />
        <label for="ban_truc_tiep" class="checkbox-label">Bán trực tiếp</label>
      </div>
    </div>
  </div>
</template>

<script setup>
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'
import TreeSelect from 'primevue/treeselect'
import Checkbox from 'primevue/checkbox'

defineProps({
  form: { type: Object, required: true },
  selectedCategoryKey: { type: [Object, String, Number], default: null },
  categoryTreeNodes: { type: Array, default: () => [] },
  manufacturerOptions: { type: Array, default: () => [] },
  positionOptions: { type: Array, default: () => [] }
})

defineEmits([
  'generate-code', 
  'generate-barcode', 
  'category-change',
  'open-unit-modal',
  'open-manufacturer-modal',
  'open-position-modal'
])
</script>
