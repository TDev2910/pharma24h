<template>
  <div class="tab-content">
    <!-- THÔNG TIN CƠ BẢN -->
    <div class="form-section">
      <div class="form-row">
        <!-- Left inputs -->
        <div class="form-column">
          
          <div class="form-row">
            <div class="form-field">
              <label class="field-label"><span>Mã dịch vụ</span></label>
              <div class="input-group">
                <InputText v-model="form.ma_dich_vu" placeholder="Tự động" readonly class="field-input readonly-input" />
                <Button type="button" icon="pi pi-refresh" label="Tạo mã" severity="secondary" size="small" @click="$emit('generateCode')" />
              </div>
            </div>

            <div class="form-field">
              <label class="field-label"><span>Nhóm hàng</span> <span class="text-danger">*</span></label>
              <TreeSelect 
                :modelValue="selectedCategoryKey" 
                @update:modelValue="val => $emit('update:selectedCategoryKey', val)"
                @change="$emit('categoryChange', $event)"
                :options="categoryTreeNodes" 
                placeholder="Chọn nhóm hàng"
                class="modern-treeselect w-full" 
                :class="{ 'p-invalid': errors.nhom_hang_id }"
                selectionMode="single" 
              />
              <small v-if="errors.nhom_hang_id" class="p-error">{{ errors.nhom_hang_id }}</small>
            </div>
          </div>

          <div class="form-field full-width">
            <label class="field-label"><span>Tên dịch vụ</span> <span class="text-danger">*</span></label>
            <InputText v-model="form.ten_dich_vu" placeholder="Nhập tên dịch vụ y tế" class="field-input" :class="{ 'p-invalid': errors.ten_dich_vu }" />
            <small v-if="errors.ten_dich_vu" class="p-error">{{ errors.ten_dich_vu }}</small>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="field-label"><span>Bác sĩ phụ trách</span> <span class="text-danger">*</span></label>
              <Dropdown v-model="form.doctor_id" :options="doctorOptions" optionLabel="name" optionValue="id"
                placeholder="Chọn bác sĩ..." showClear filter class="field-input"
                :class="{ 'p-invalid': errors.doctor_id }">
                <template #option="slotProps">
                  <div style="display: flex; flex-direction: column;">
                    <div style="color: #374151; padding-bottom: 2px;">{{ slotProps.option.name }}</div>
                    <div style="font-size: 13px; color: #6b7280;">{{ slotProps.option.doctor_code }} - {{ slotProps.option.specialty }}</div>
                  </div>
                </template>
              </Dropdown>
              <small v-if="errors.doctor_id" class="p-error">{{ errors.doctor_id }}</small>
            </div>

            <div class="form-field">
              <label class="field-label"><span>Hình thức</span></label>
              <Dropdown v-model="form.hinh_thuc" :options="serviceTypes" optionLabel="label"
                optionValue="value" placeholder="Chọn hình thức" class="field-input" />
            </div>
          </div>

        </div>
        
        <!-- Right image upload via Slot -->
        <slot name="image"></slot>
      </div>
    </div>

    <!-- Thiết lập dịch vụ -->
    <fieldset>
      <legend>Thiết lập dịch vụ</legend>
      <div class="form-row three-columns">
        <div class="form-field">
          <label class="field-label"><span>Chi phí dịch vụ (VND)</span> <span class="text-danger">*</span></label>
          <InputNumber v-model="form.gia_dich_vu" mode="currency" currency="VND" locale="vi-VN" class="price-input" :class="{ 'p-invalid': errors.gia_dich_vu }" />
          <small v-if="errors.gia_dich_vu" class="p-error">{{ errors.gia_dich_vu }}</small>
        </div>

        <div class="form-field">
          <label class="field-label"><span>Thời gian (phút)</span></label>
          <InputNumber v-model="form.thoi_gian_thuc_hien" placeholder="Ví dụ: 30" :min="1" class="price-input w-full" />
        </div>

        <div class="form-field">
          <label class="field-label"><span>Trạng thái</span></label>
          <Dropdown v-model="form.trang_thai" :options="statusOptions" optionLabel="label"
            optionValue="value" class="field-input" :class="{ 'p-invalid': errors.trang_thai }">
          </Dropdown>
          <small v-if="errors.trang_thai" class="p-error">{{ errors.trang_thai }}</small>
        </div>
      </div>
    </fieldset>

  </div>
</template>

<script setup>
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'
import TreeSelect from 'primevue/treeselect'

defineProps({
  form: Object,
  errors: Object,
  categoryTreeNodes: Array,
  doctorOptions: Array,
  serviceTypes: Array,
  statusOptions: Array,
  selectedCategoryKey: [Object, String, Number]
})

defineEmits(['categoryChange', 'generateCode', 'update:selectedCategoryKey'])
</script>
