import { useForm, router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { defaultFormState } from "../../Constants/serviceConstants";

export function useServiceForm(onSuccessCallback) {
    const toast = useToast()

    const form = useForm({ ...defaultFormState })

    const saveService = (event) => {
        if (event) {
            event.preventDefault()
            event.stopPropagation()
        }

        form.clearErrors()

        form.post('/admin/services', {
            forceFormData: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Thành công',
                    detail: 'Dịch vụ đã được lưu thành công!',
                    life: 3000
                })

                if (onSuccessCallback) onSuccessCallback()

                setTimeout(() => {
                    router.reload({ only: [] })
                }, 300)
            },
            onError: (errors) => {
                toast.add({
                    severity: 'error',
                    summary: 'Lỗi validation',
                    detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
                    life: 5000
                })
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
        saveService,
        resetForm
    }
}