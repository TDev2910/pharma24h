import { useForm, router } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import { defaultFormState } from '../../Constants/medicineConstants'

export function useMedicineForm(onSuccessCallback) {
  const toast = useToast()
  
  // Khởi tạo state của form với dữ liệu mặc định từ Constants
  const form = useForm({ ...defaultFormState })

  const saveMedicine = (event) => {
    // Ngăn hành vi submit mặc định nếu được gọi qua thẻ form
    if (event) {
      event.preventDefault()
      event.stopPropagation()
    }

    form.clearErrors()

    // Sử dụng form.post của Inertia thay vì Axios thủ công
    form.post('/admin/medicines', {
      forceFormData: true, // Tự động xử lý FormData nếu có File (ảnh)
      onSuccess: () => {
        toast.add({ severity: 'success', summary: 'Thành công', detail: 'Thuốc đã được thêm thành công!', life: 3000 })
        
        // Gọi callback nếu có (vd: đóng modal, reset tab)
        if (onSuccessCallback) onSuccessCallback()

        // Reload trang để dữ liệu mới nhất được cập nhật trên Table
        setTimeout(() => {
          router.reload({ only: [] })
        }, 300)
      },
      onError: (errors) => {
        toast.add({ severity: 'error', summary: 'Lỗi validation', detail: 'Vui lòng kiểm tra lại thông tin nhập vào', life: 5000 })
        console.error('Validation Errors:', errors)
      }
    })
  }

  const resetForm = () => {
    form.reset()
    form.clearErrors()
  }

  return {
    form,
    saveMedicine,
    resetForm
  }
}
