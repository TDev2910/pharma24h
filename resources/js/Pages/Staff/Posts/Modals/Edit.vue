<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import Editor from 'primevue/editor';

const props = defineProps({
    post: Object,
    categories: Array,
    baseUrl: String,
});

const emit = defineEmits(['close', 'success']);
const toast = useToast();

const form = useForm({
    id: props.post.id,
    title: props.post.title,
    category_id: props.post.category_id,
    summary: props.post.summary,
    content: props.post.content,
    thumbnail: null,
    gallery: [],
    is_published: !!props.post.is_published,
    _method: 'PUT'
});

const thumbnailPreview = ref(props.post.thumbnail ? `/storage/${props.post.thumbnail}` : null);

const currentImages = computed(() => props.post.images || []);

const onThumbnailSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.thumbnail = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
};

const onGallerySelect = (event) => {
    form.gallery = Array.from(event.target.files);
};

const deleteGalleryImage = (imgId) => {
    if (confirm('Xóa ảnh này khỏi thư viện?')) {
        router.delete(`${props.baseUrl}/images/${imgId}`, {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Đã xóa ảnh', life: 2000 });
                // Note: Inertia will refresh props, and if parent passes updated post, currentImages will update.
            }
        });
    }
};

const submit = () => {
    form.post(`${props.baseUrl}/${props.post.id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã cập nhật bài viết', life: 3000 });
            emit('success');
            emit('close');
        }
    });
};
</script>

<template>
    <div class="modal-overlay" @click.self="$emit('close')">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Cập nhật bài viết</h3>
                <button class="modal-close" @click="$emit('close')">
                    <i class="pi pi-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- Title -->
                <div class="form-group">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" v-model="form.title" class="form-input" placeholder="Nhập tiêu đề..."
                        :class="{ 'border-error': form.errors.title }" />
                    <small class="error-message" v-if="form.errors.title">{{ form.errors.title }}</small>
                </div>

                <!-- Row: Category & Status -->
                <div class="form-row">
                    <div class="col-half">
                        <label class="form-label">Danh mục</label>
                        <select v-model="form.category_id" class="form-select"
                            :class="{ 'border-error': form.errors.category_id }">
                            <option :value="null" disabled>Chọn danh mục</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}
                            </option>
                        </select>
                        <small class="error-message" v-if="form.errors.category_id">{{ form.errors.category_id
                            }}</small>
                    </div>
                    <div class="col-half">
                        <label class="form-label">Trạng thái</label>
                        <select v-model="form.is_published" class="form-select">
                            <option :value="false">Nháp</option>
                            <option :value="true">Công khai</option>
                        </select>
                    </div>
                </div>

                <!-- Summary -->
                <div class="form-group">
                    <label class="form-label">Tóm tắt</label>
                    <textarea v-model="form.summary" class="form-textarea short-textarea"
                        placeholder="Nhập tóm tắt..."></textarea>
                </div>

                <!-- Content -->
                <div class="form-group">
                    <label class="form-label">Nội dung</label>
                    <div class="editor-wrapper" :class="{ 'border-error': form.errors.content }">
                        <Editor v-model="form.content" editorStyle="height: 300px"
                            placeholder="Nhập nội dung bài viết..." />
                    </div>
                    <small class="error-message" v-if="form.errors.content">{{ form.errors.content }}</small>
                </div>

                <!-- Image Upload Section -->
                <div class="form-row image-upload-section">
                    <!-- Thumbnail -->
                    <div class="col-half">
                        <label class="form-label">Ảnh chính</label>
                        <div class="file-upload-wrapper">
                            <input type="file" accept="image/*" @change="onThumbnailSelect" class="form-file-input" />
                            <div v-if="thumbnailPreview" class="preview-container mt-2">
                                <img :src="thumbnailPreview" alt="Thumbnail Preview" class="preview-image" />
                            </div>
                        </div>
                    </div>

                    <!-- Gallery -->
                    <div class="col-half">
                        <label class="form-label">Ảnh phụ</label>
                        <div class="file-upload-wrapper">
                            <input type="file" accept="image/*" multiple @change="onGallerySelect"
                                class="form-file-input" />
                            <div v-if="currentImages.length" class="gallery-preview-grid mt-2">
                                <div v-for="img in currentImages" :key="img.id" class="gallery-item">
                                    <img :src="`/storage/${img.path}`" alt="Gallery" />
                                    <button @click.prevent="deleteGalleryImage(img.id)" class="delete-img-btn">
                                        <i class="pi pi-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel" @click="$emit('close')">Hủy</button>
                <button class="btn-submit" @click="submit" :disabled="form.processing">
                    Cập nhật
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "../../../../../css/Staff/posts/modals.css";
</style>