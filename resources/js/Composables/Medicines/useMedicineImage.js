import { ref } from 'vue'

export function useMedicineImage(form) {
  const imagePreview = ref(null)

  const handleImageUpload = () => {
    // Tạo input file ảo thay vì viết trong template để giữ template gọn gàng
    const input = document.createElement('input')
    input.type = 'file'
    input.accept = 'image/*'
    
    input.onchange = (event) => {
      const file = event.target.files[0]
      if (!file) return
      
      // Basic validation front-end
      if (!file.type.startsWith('image/')) {
        alert('Chỉ chấp nhận định dạng file ảnh!')
        return
      }
      if (file.size > 2 * 1024 * 1024) {
        alert('Dung lượng file quá lớn! Vui lòng chọn ảnh tối đa 2MB.')
        return
      }
      
      // Gán vào state của Inertia form
      form.image = file
      
      // Khởi tạo preview
      const reader = new FileReader()
      reader.onload = (e) => {
        imagePreview.value = e.target.result
      }
      reader.readAsDataURL(file)
    }
    
    // Kích hoạt dialog duyệt file
    input.click()
  }

  const removeImage = () => {
    form.image = null
    imagePreview.value = null
  }

  return {
    imagePreview,
    handleImageUpload,
    removeImage
  }
}
