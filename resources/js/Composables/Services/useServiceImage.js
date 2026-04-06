import { ref } from "vue"
import { useToast } from "primevue/usetoast"

const MAX_FILE_SIZE = 2 * 1024 * 1024 // 2MB
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']

export function useServiceImage(form) {
    const toast = useToast()
    const imagePreview = ref(null)
    const imageError = ref(null)

    /**
     * Kiểm tra tính hợp lệ của file ảnh
     */
    const validateImage = (file) => {
        imageError.value = null

        if (!ALLOWED_TYPES.includes(file.type)) {
            imageError.value = 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WebP)'
            toast.add({
                severity: 'warn',
                summary: 'File không hợp lệ',
                detail: imageError.value,
                life: 3000
            })
            return false
        }

        if (file.size > MAX_FILE_SIZE) {
            imageError.value = 'Kích thước ảnh tối đa là 2MB'
            toast.add({
                severity: 'warn',
                summary: 'File quá lớn',
                detail: imageError.value,
                life: 3000
            })
            return false
        }

        return true
    }

    /**
     * Xử lý upload ảnh: validate → preview → gán vào form
     */
    const handleImageUpload = (file) => {
        if (!file) return

        if (!validateImage(file)) return

        // Tạo preview
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)

        // Gán file vào form để submit
        form.image = file
    }

    /**
     * Xóa ảnh đã chọn
     */
    const removeImage = () => {
        form.image = null
        imagePreview.value = null
        imageError.value = null
    }

    return {
        imagePreview,
        imageError,
        handleImageUpload,
        removeImage,
    }
}
