<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';

const props = defineProps({ categories: Object, baseUrl: String });
const toast = useToast();

// State
const categoryDialog = ref(false);
const deleteDialog = ref(false);
const isEditing = ref(false);
const categoryToDelete = ref(null);

// Form
const form = useForm({
    id: null,
    name: '',
    description: '',
});


const openNew = () => {
    form.reset();
    isEditing.value = false;
    categoryDialog.value = true;
};

const editCategory = (category) => {
    form.id = category.id;
    form.name = category.name;
    form.description = category.description;
    isEditing.value = true;
    categoryDialog.value = true;
};

const saveCategory = () => {
    if (isEditing.value) {
        form.put(`${props.baseUrl}/${form.id}`, {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã cập nhật danh mục', life: 3000 });
                form.reset();
            }
        });
    } else {
        form.post(props.baseUrl, {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã tạo danh mục mới', life: 3000 });
                form.reset();
            }
        });
    }
};

const confirmDelete = (cat) => {
    categoryToDelete.value = cat;
    deleteDialog.value = true;
};

const deleteCategory = () => {
    router.delete(`${props.baseUrl}/${categoryToDelete.value.id}`, {
        onSuccess: () => {
            deleteDialog.value = false;
            toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã xóa danh mục', life: 3000 });
        },
        onError: () => {
            deleteDialog.value = false;
            toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Không thể xóa danh mục này', life: 3000 });
        }
    });
};
</script>

<template>
    <div class="page-container">
        <Toast />

        <div class="card">
            <!-- Header Section -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Quản lý danh mục</h1>
                    <p class="page-subtitle">Quản lý các danh mục bài viết</p>
                </div>
                <button class="btn-primary" @click="openNew">
                    <i class="pi pi-plus"></i>
                    Thêm danh mục
                </button>
            </div>

            <!-- Toolbar/Search -->
            <div class="toolbar">
                <div class="search-wrapper">
                    <i class="pi pi-search search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm danh mục..." class="search-input" />
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 200px;">Tên danh mục</th>
                            <th>Mô tả</th>
                            <th class="text-center" style="width: 120px;">Số bài viết</th>
                            <th style="width: 150px;">Ngày tạo</th>
                            <th class="text-right" style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(category, index) in categories.data" :key="category.id">
                            <td class="text-gray-500">{{ index + 1 }}</td>
                            <td class="font-bold text-dark">{{ category.name }}</td>
                            <td class="text-gray-500">{{ category.description }}</td>
                            <td class="text-center font-medium">{{ category.posts_count }}</td>
                            <td class="text-gray-500">{{ new Date(category.created_at).toLocaleDateString('vi-VN')
                                }}</td>
                            <td class="text-right">
                                <div class="actions">
                                    <button class="btn-icon" @click="editCategory(category)">
                                        <i class="pi pi-pencil"></i>
                                    </button>
                                    <button class="btn-icon btn-delete" @click="confirmDelete(category)">
                                        <i class="pi pi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categories.data.length === 0">
                            <td colspan="6" class="text-center py-8 text-gray-500">Không có dữ liệu</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modals -->
            <!-- Custom Modal -->
            <div v-if="categoryDialog" class="modal-overlay" @click.self="categoryDialog = false">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 class="modal-title">{{ isEditing ? 'Cập nhật danh mục' : 'Tạo danh mục mới' }}</h3>
                        <button class="modal-close" @click="categoryDialog = false">
                            <i class="pi pi-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Tên danh mục</label>
                            <input type="text" v-model="form.name" class="form-input" placeholder="Nhập tên danh mục..."
                                :class="{ 'border-error': form.errors.name }" autofocus />
                            <small class="error-message" v-if="form.errors.name">{{ form.errors.name }}</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mô tả</label>
                            <textarea v-model="form.description" class="form-textarea" placeholder="Nhập mô tả..."
                                rows="4"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn-cancel" @click="categoryDialog = false">Hủy</button>
                        <button class="btn-submit" @click="saveCategory" :disabled="form.processing">
                            {{ isEditing ? 'Cập nhật' : 'Tạo mới' }}
                        </button>
                    </div>
                </div>
            </div>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Xác nhận" :modal="true">
                <div class="flex items-center p-2">
                    <i class="pi pi-exclamation-triangle mr-3 text-red-500 text-2xl" />
                    <span v-if="categoryToDelete">Bạn có chắc muốn xóa <b>{{ categoryToDelete.name }}</b>?</span>
                </div>
                <template #footer>
                    <div class="flex justify-end gap-2">
                        <Button label="Không" icon="pi pi-times" text @click="deleteDialog = false" />
                        <Button label="Xóa" icon="pi pi-check" severity="danger" @click="deleteCategory" />
                    </div>
                </template>
            </Dialog>
        </div>
    </div>
</template>

<style scoped>
.page-container {
    padding: 24px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background-color: transparent;
    /* Assuming layout has bg */
}

/* Card */
.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    padding: 30px;
}

/* Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 6px 0;
    line-height: 1.2;
}

.page-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.btn-primary {
    background-color: #3b82f6;
    /* Blue 500 */
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.btn-primary:hover {
    background-color: #2563eb;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

/* Toolbar */
.toolbar {
    margin-bottom: 24px;
}

.search-wrapper {
    position: relative;
    width: 380px;
    max-width: 100%;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 16px;
}

.search-input {
    width: 100%;
    padding: 12px 16px 12px 42px;
    /* Left padding for icon */
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    color: #1f2937;
    transition: all 0.2s;
    background-color: #fff;
}

.search-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

/* Table */
.table-container {
    overflow-x: auto;
}

.custom-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    text-align: left;
}

.custom-table th {
    padding: 16px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    /* Gray text for headers */
    border-bottom: 1px solid #f3f4f6;
    background-color: #fff;
    white-space: nowrap;
}

.custom-table td {
    padding: 16px 12px;
    font-size: 14px;
    color: #1f2937;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.custom-table tr:hover td {
    background-color: #f9fafb;
}

.custom-table tr:last-child td {
    border-bottom: none;
}

/* Typography Helpers */
.font-bold {
    font-weight: 600;
}

.font-medium {
    font-weight: 500;
}

.text-dark {
    color: #111827;
}

.text-gray-500 {
    color: #6b7280;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

/* Action Buttons in Table */
.actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    background: transparent;
    /* Default transparent */
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: #64748b;
    /* Default gray */
}

.btn-icon:hover {
    background-color: #f1f5f9;
    color: #3b82f6;
    /* Blue on hover */
}

.btn-icon.btn-delete:hover {
    background-color: #fee2e2;
    color: #ef4444;
    /* Red on hover */
}

.btn-icon i {
    font-size: 15px;
}

/* Dialog Footer Override to align with our custom style */
:deep(.p-button.p-button-text) {
    color: #64748b;
}

:deep(.p-button.p-button-text:hover) {
    background: #f1f5f9;
    color: #1e293b;
}

/* Custom Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-container {
    background-color: #ffffff;
    width: 500px;
    max-width: 90%;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 24px;
    animation: modal-fade-in 0.2s ease-out;
}

@keyframes modal-fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #6b7280;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.modal-close:hover {
    color: #111827;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 500;
    font-size: 14px;
    color: #111827;
    margin-bottom: 8px;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    color: #374151;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    background-color: #fff;
    font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder,
.form-textarea::placeholder {
    color: #9ca3af;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.border-error {
    border-color: #ef4444;
}

.error-message {
    font-size: 12px;
    color: #ef4444;
    margin-top: 4px;
    display: block;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 8px;
}

.btn-cancel {
    padding: 10px 20px;
    background-color: #f3f4f6;
    color: #111827;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-cancel:hover {
    background-color: #e5e7eb;
}

.btn-submit {
    padding: 10px 20px;
    background-color: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-submit:hover {
    background-color: #2563eb;
}
</style>