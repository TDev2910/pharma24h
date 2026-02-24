<script setup>
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import TreeSelect from 'primevue/treeselect';

// 1. NHẬN THÊM PROPS `parentCategories` TỪ CONTROLLER
const props = defineProps({
    categories: Object,
    parentCategories: Array,
    categoryTree: Array,
    baseUrl: String
});

const toast = useToast();

// State
const categoryDialog = ref(false);
const deleteDialog = ref(false);
const isEditing = ref(false);
const categoryToDelete = ref(null);

// Form
// 2. THÊM TRƯỜNG `parent_id` VÀO FORM
const selectedParentNode = ref(null);

const form = useForm({
    id: null,
    name: '',
    description: '',
    parent_id: null,
});

watch(selectedParentNode, (newValue) => {
    if (newValue && Object.keys(newValue).length > 0) {
        // Lấy key đầu tiên trong object (chính là ID của danh mục)
        form.parent_id = parseInt(Object.keys(newValue)[0]);
    } else {
        form.parent_id = null;
    }
});

const openNew = () => {
    form.reset();
    selectedParentNode.value = null;
    isEditing.value = false;
    categoryDialog.value = true;
};

const editCategory = (category) => {
    form.id = category.id;
    form.name = category.name;
    form.description = category.description;
    form.parent_id = category.parent_id;
    if (category.parent_id) {
        selectedParentNode.value = { [category.parent_id]: true };
    } else {
        selectedParentNode.value = null;
    }
    isEditing.value = true;
    categoryDialog.value = true;
};

const saveCategory = () => {
    if (isEditing.value) {
        form.put(`${props.baseUrl}/${form.id}`, {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã cập nhật', life: 3000 });
                form.reset();
            },
            onError: (errors) => {
                if (errors.error) toast.add({ severity: 'error', summary: 'Lỗi', detail: errors.error, life: 3000 });
            }
        });
    } else {
        form.post(props.baseUrl, {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã tạo mới', life: 3000 });
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
        onError: (errors) => {
            deleteDialog.value = false;
            // Hiển thị thông báo lỗi chi tiết từ Controller (Vd: Đang chứa bài viết, Đang chứa danh mục con)
            const errorMsg = errors.error || 'Không thể xóa danh mục này';
            toast.add({ severity: 'error', summary: 'Lỗi', detail: errorMsg, life: 4000 });
        }
    });
};
</script>

<template>
    <div class="page-container">
        <Toast />

        <div class="card">
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

            <div class="toolbar">
                <div class="search-wrapper">
                    <i class="pi pi-search search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm danh mục..." class="search-input" />
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 250px;">Tên danh mục</th>
                            <th style="width: 150px;">Cấp độ</th>
                            <th>Mô tả</th>
                            <th class="text-center" style="width: 100px;">Bài viết</th>
                            <th style="width: 120px;">Ngày tạo</th>
                            <th class="text-right" style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(category, index) in (categories.data || categories)" :key="category.id">
                            <td class="text-gray-500">{{ index + 1 }}</td>
                            <td class="font-bold text-dark">
                                <div :style="{ paddingLeft: (category.level * 24) + 'px' }" class="flex items-center">
                                    <span v-if="category.level > 0" class="text-gray-300 mr-2">└─</span>
                                    {{ category.name }}
                                </div>
                            </td>

                            <td>
                                <span v-if="category.parent" class="parent-badge">
                                    <i class="pi pi-sitemap text-[10px] mr-1"></i> {{ category.parent.name }}
                                </span>
                                <span v-else class="root-badge">
                                    Danh mục gốc
                                </span>
                            </td>

                            <td class="text-gray-500 text-sm italic">{{ category.description || 'Chưa có mô tả' }}</td>
                            <td class="text-center font-medium">{{ category.posts_count }}</td>
                            <td class="text-gray-500 text-sm">{{ new
                                Date(category.created_at).toLocaleDateString('vi-VN') }}</td>
                            <td class="text-right">
                                <div class="actions">
                                    <button class="btn-icon" @click="editCategory(category)" title="Chỉnh sửa">
                                        <i class="pi pi-pencil"></i>
                                    </button>
                                    <button class="btn-icon btn-delete" @click="confirmDelete(category)" title="Xóa">
                                        <i class="pi pi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="(categories.data || categories).length === 0">
                            <td colspan="6" class="text-center py-8 text-gray-500">Không có dữ liệu</td>
                        </tr>
                    </tbody>
                </table>
            </div>

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
                            <label class="form-label">Danh mục cha</label>
                            <TreeSelect v-model="selectedParentNode" :options="categoryTree"
                                placeholder="Chọn danh mục cha (Để trống nếu là gốc)" class="w-full"
                                :class="{ 'p-invalid': form.errors.parent_id }" display="comma" />
                            <small class="text-gray-500 mt-1 block italic text-xs">
                                Chọn danh mục cha nếu đây là danh mục con.
                            </small>
                            <small class="error-message" v-if="form.errors.parent_id">{{ form.errors.parent_id
                                }}</small>
                        </div>

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
@import "../../../../css/Staff/categories/index.css";
@import "../../../../css/Staff/categories/modals.css";
</style>