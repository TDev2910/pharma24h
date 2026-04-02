import { ref } from 'vue'

export function useGoodsImage(form) {
  const imagePreview = ref(null)

  const handleImageUpload = (file) => {
    if (!file) return

    // Preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)

    // Set to form
    form.image = file
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
