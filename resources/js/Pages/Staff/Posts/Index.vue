<script setup>
import { ref, watch, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

// PrimeVue Components
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';

// Modals
import CreateModal from './Modals/Create.vue';
import EditModal from './Modals/Edit.vue';

const props = defineProps({
    posts: Object,
    categories: Array,
    baseUrl: String,
    filters: Object
});

const toast = useToast();

// State
const createDialog = ref(false);
const editDialog = ref(false);
const deleteDialog = ref(false);
const selectedPostId = ref(null);
const postToDelete = ref(null);

const selectedPost = computed(() => {
    if (selectedPostId.value && props.posts?.data) {
        return props.posts.data.find(p => p.id === selectedPostId.value) || null;
    }
    return null;
});

// Search & Filters
const search = ref(props.filters?.search || '');
const filterCategory = ref(props.filters?.category_id || '');
const filterStatus = ref(props.filters?.status || '');

watch([search, filterCategory, filterStatus], debounce(([valSearch, valCat, valStatus]) => {
    router.get(props.baseUrl, {
        search: valSearch,
        category_id: valCat,
        status: valStatus
    }, { preserveState: true, replace: true });
}, 300));

// Helpers
const truncate = (str, n) => {
    return (str && str.length > n) ? str.substr(0, n - 1) + '...' : str;
};

// HÀM KHẮC PHỤC LỖI NGÀY THÁNG
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    // Nếu dateString không hợp lệ (ví dụ: '13/02/2026'), trả về nguyên gốc
    if (isNaN(date.getTime())) return dateString;
    return date.toISOString().split('T')[0];
};

// --- Actions ---

const openNew = () => {
    createDialog.value = true;
};

const editPost = (post) => {
    selectedPostId.value = post.id;
    editDialog.value = true;
};

const confirmDelete = (post) => {
    postToDelete.value = post;
    deleteDialog.value = true;
};

const deletePost = () => {
    router.delete(`${props.baseUrl}/${postToDelete.value.id}`, {
        onSuccess: () => {
            deleteDialog.value = false;
            toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã xóa bài viết', life: 3000 });
        }
    });
};

const onSaveSuccess = () => {
    createDialog.value = false;
    editDialog.value = false;
    selectedPostId.value = null;
};
</script>

<template>
    <div class="page-container">
        <Toast />

        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Quản lý bài viết</h1>
                <p class="page-subtitle">Tạo, chỉnh sửa và quản lý các bài viết</p>
            </div>
            <button class="btn-primary" @click="openNew">
                <i class="pi pi-plus"></i>
                Thêm bài viết
            </button>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <div class="search-wrapper">
                <i class="pi pi-search search-icon"></i>
                <input type="text" v-model="search" placeholder="Tìm kiếm bài viết..." class="search-input" />
            </div>

            <div class="filter-dropdowns">
                <select class="custom-select" v-model="filterCategory">
                    <option value="">Tất cả danh mục</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>

                <select class="custom-select" v-model="filterStatus">
                    <option value="">Tất cả</option>
                    <option :value="true">Đã xuất bản</option>
                    <option :value="false">Nháp</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 35%;">Tiêu đề</th>
                        <th style="width: 15%;">Danh mục</th>
                        <th style="width: 15%;">Trạng thái</th>
                        <th style="width: 10%;">Ngày tạo</th>
                        <th style="width: 10%;">Cập nhật</th>
                        <th class="text-right" style="width: 10%;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(post, index) in posts.data" :key="post.id">
                        <td class="text-gray-500">{{ index + 1 }}</td>
                        <td>
                            <div class="post-info">
                                <div class="post-title">{{ post.title }}</div>
                                <div class="post-summary">{{ truncate(post.summary, 60) }}</div>
                            </div>
                        </td>
                        <td class="text-gray-700">{{ post.category?.name || '---' }}</td>
                        <td>
                            <span class="status-badge" :class="post.is_published ? 'status-published' : 'status-draft'">
                                {{ post.is_published ? 'Đã xuất bản' : 'Nháp' }}
                            </span>
                        </td>
                        <td class="text-gray-500">{{ formatDate(post.created_at) }}</td>
                        <td class="text-gray-500">{{ formatDate(post.updated_at) }}</td>
                        <td class="text-right">
                            <div class="actions">
                                <button class="btn-icon" title="Xem"><i class="pi pi-eye"></i></button>
                                <button class="btn-icon" @click="editPost(post)" title="Sửa"><i
                                        class="pi pi-pencil"></i></button>
                                <button class="btn-icon btn-delete" @click="confirmDelete(post)" title="Xóa"><i
                                        class="pi pi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="posts.data.length === 0">
                        <td colspan="7" class="text-center py-8 text-gray-500">Không tìm thấy bài viết nào</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="posts.links.length > 3">
            <template v-for="(link, k) in posts.links" :key="k">
                <Link v-if="link.url" :href="link.url" class="page-link" :class="{ 'active': link.active }">
                    <span v-html="link.label"></span>
                </Link>
                <span v-else class="page-link disabled" v-html="link.label"></span>
            </template>
        </div>

        <!-- Modals -->
        <CreateModal v-if="createDialog" :categories="categories" :baseUrl="baseUrl" @close="createDialog = false"
            @success="onSaveSuccess" />

        <EditModal v-if="editDialog" :post="selectedPost" :categories="categories" :baseUrl="baseUrl"
            @close="editDialog = false" @success="onSaveSuccess" />

        <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Xác nhận" :modal="true">
            <div class="flex items-center p-4">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500 text-4xl" />
                <span v-if="postToDelete" class="text-lg">Bạn có chắc muốn xóa bài viết <b>{{ postToDelete.title }}</b>
                    không?</span>
            </div>
            <template #footer>
                <Button label="Không" icon="pi pi-times" text @click="deleteDialog = false" />
                <Button label="Xóa" icon="pi pi-check" severity="danger" @click="deletePost" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
@import "../../../../css/Staff/posts/index.css";
</style>