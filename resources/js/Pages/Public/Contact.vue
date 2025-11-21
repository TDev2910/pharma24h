<template>
  <div class="page-wrapper" style="margin-top: 100px;margin-right: 200px;">
    <Header :auth="auth" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">

          <div class="mb-5">
            <h1 class="fw-bold mb-2">Send Us a Message</h1>
            <p class="text-muted">Please fill in the form below to get in touch with us.</p>
          </div>

          <form @submit.prevent="handleSubmit">
            <div class="row g-3">

              <div class="col-md-6">
                <InputText v-model="form.firstName" placeholder="First name" class="w-100 p-3 bg-light border-0" />
              </div>

              <div class="col-md-6">
                <InputText v-model="form.lastName" placeholder="Last name" class="w-100 p-3 bg-light border-0" />
              </div>

              <div class="col-md-6">
                <InputText v-model="form.email" placeholder="Email address" class="w-100 p-3 bg-light border-0" />
              </div>

              <div class="col-md-6">
                <InputText v-model="form.phone" placeholder="Phone Number" class="w-100 p-3 bg-light border-0" />
              </div>

              <div class="col-12">
                <Textarea v-model="form.message" placeholder="Message" rows="5" class="w-100 p-3 bg-light border-0"
                  autoResize />
              </div>

              <div class="col-12">
                <div class="d-flex align-items-center gap-2">
                  <Checkbox v-model="form.agree" inputId="terms" :binary="true" />
                  <label for="terms" class="text-dark">
                    I've read and agree with <a href="#" class="text-decoration-underline text-dark fw-bold">Terms of
                      Service</a> and <a href="#" class="text-decoration-underline text-dark fw-bold">Privacy Policy</a>
                  </label>
                </div>
              </div>

              <div class="col-10 mt-4">
                <Button style="border-radius: 18px;" type="submit" label="Submit"
                  class="bg-black text-white border-0 px-5 py-3 fw-bold" :loading="loading" />
              </div>

            </div>
          </form>

        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import { ref, reactive } from 'vue';

// Import PrimeVue Components (Đảm bảo bạn đã đăng ký global hoặc import tại đây)
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import { useToast } from "primevue/usetoast"; // Nếu bạn muốn dùng thông báo

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) }
})

// State quản lý Form
const loading = ref(false);
const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  message: '',
  agree: false
});

// Xử lý Submit
const handleSubmit = () => {
  if (!form.agree) {
    alert("Vui lòng đồng ý với điều khoản!");
    return;
  }

  loading.value = true;

  // Giả lập gọi API
  setTimeout(() => {
    console.log("Form Data:", form);
    loading.value = false;
    alert("Gửi thành công!");
    // Reset form nếu cần
  }, 2000);
};
</script>

<style scoped>
/* Tùy chỉnh đè style của PrimeVue để giống ảnh mẫu hơn */
:deep(.p-inputtext) {
  background-color: #f8f9fa;
  /* Màu xám nhạt giống ảnh */
  color: #495057;
}

:deep(.p-inputtext:focus) {
  background-color: #fff;
  box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
  /* Focus bóng mờ nhẹ */
  border-color: #ced4da;
}

/* Style cho Checkbox khi checked */
:deep(.p-checkbox .p-checkbox-box.p-highlight) {
  background-color: #000;
  border-color: #000;
}

/* Style cho Button */
:deep(.p-button) {
  border-radius: 4px;
  /* Bo góc nhẹ giống ảnh */
}
</style>