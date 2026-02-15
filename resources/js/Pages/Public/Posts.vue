<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    categories: { type: Array, default: () => [] },
    featuredPosts: { type: Array, default: () => [] },
    latestPosts: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const getCategoryMeta = (slug) => {
    const metaMap = {
        'tim-mach': { icon: '❤️', color: 'red' },
        'dinh-duong': { icon: '🍏', color: 'green' },
        'thuoc-dieu-tri': { icon: '💊', color: 'blue' },
        'giac-ngu': { icon: '🌙', color: 'orange' },
        'kham-benh': { icon: '🩺', color: 'blue' },
        'thao-duoc': { icon: '🍃', color: 'green' },

        'lam-dep': { icon: '💄', color: 'pink' },
        'me-va-be': { icon: '👶', color: 'purple' },
        'covid-19': { icon: '🦠', color: 'red' },

        'default': { icon: '⚕️', color: 'blue' }
    };
    return metaMap[slug] || metaMap['default'];
};

// categories
const processedCategories = computed(() => {
    return props.categories.map(cat => {
        const meta = getCategoryMeta(cat.slug);
        return {
            id: cat.id,
            name: cat.name,
            slug: cat.slug,
            count: cat.posts_count || 0,
            icon: meta.icon
        };
    });
});

// featured Posts
const processedFeaturedPosts = computed(() => {
    return props.featuredPosts.map(post => {
        const meta = getCategoryMeta(post.categorySlug);
        return {
            ...post,
            desc: post.summary,
            catColor: meta.color
        };
    });
});

// 3. Latest Posts
const processedLatestPosts = computed(() => {
    return props.latestPosts.map(post => {
        const meta = getCategoryMeta(post.categorySlug);
        return {
            ...post,
            desc: post.summary,
            catColor: meta.color
        };
    });
});

// --- ACTIONS ---
const handleCategoryClick = (category) => {
    router.get('/posts', { category: category.slug }, { preserveState: true, preserveScroll: true });
};

const loadMore = () => {
    console.log('Load more functionality to be implemented with pagination');
};
</script>

<template>

    <Head title="Góc sức khỏe" />

    <section class="health-corner-container" style="margin-top: 100px;">
        <div class="banner-wrapper">
            <div class="banner-content">
                <div class="banner-info">
                    <div class="cross-bg-icon">+</div>

                    <div class="info-box">
                        <h2>Góc Sức Khỏe</h2>
                        <p>Cập nhật kiến thức sức khỏe, dinh dưỡng và lối sống lành mạnh mỗi ngày</p>
                    </div>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQwTFBgKETz62uwA5D0Tg6bC0RQNHEcEnLJ_Q&s"
                        alt="Medicine" class="sub-img" />
                </div>

                <div class="banner-image">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQwTFBgKETz62uwA5D0Tg6bC0RQNHEcEnLJ_Q&s"
                        alt="Doctor consulting" />
                </div>
            </div>
        </div>

        <div class="category-wrapper">
            <h3 class="section-title">Danh mục sức khỏe</h3>

            <div class="category-grid">
                <div v-for="item in processedCategories" :key="item.id" class="category-card"
                    @click="handleCategoryClick(item)">
                    <div class="icon-circle">
                        <span class="icon">{{ item.icon }}</span>
                    </div>
                    <h4 class="cat-name">{{ item.name }}</h4>
                    <small style="font-size: 10px">({{ item.slug }})</small>
                    <span class="cat-count">{{ item.count }} bài viết</span>
                </div>
            </div>
        </div>

        <!-- các bài viết nổi bật -->
        <section class="post-section">
            <h3 class="section-title">Bài viết nổi bật</h3>

            <div v-if="processedFeaturedPosts.length > 0" class="featured-grid">
                <div v-for="post in processedFeaturedPosts" :key="post.id" class="card-horizontal">
                    <div class="card-img-wrapper">
                        <img :src="post.image" :alt="post.title">
                    </div>
                    <div class="card-body">
                        <span class="badge" :class="post.catColor">{{ post.category }}</span>
                        <h4 class="card-title">{{ post.title }}</h4>
                        <!-- Use summary/desc -->
                        <p class="card-desc">{{ post.desc }}</p>
                        <div class="card-meta">
                            <span>🕒 {{ post.date }}</span>
                            <span>👁️ {{ post.views }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-4 text-gray-500">
                Chưa có bài viết nổi bật.
            </div>

            <h3 class="section-title mt-large">Tin tức mới nhất</h3>

            <div v-if="processedLatestPosts.length > 0" class="latest-grid">
                <div v-for="post in processedLatestPosts" :key="post.id" class="card-vertical">
                    <div class="card-img-wrapper">
                        <img :src="post.image" :alt="post.title">
                        <span class="badge-overlay" :class="post.catColor">{{ post.category }}</span>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">{{ post.title }}</h4>
                        <p class="card-desc">{{ post.desc }}</p>
                        <div class="card-meta">
                            <span>🕒 {{ post.date }}</span>
                            <span>👁️ {{ post.views }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-4 text-gray-500">
                Chưa có bài viết mới.
            </div>

            <div class="action-area">
                <button @click="loadMore" class="btn-load-more">Xem thêm bài viết</button>
            </div>
        </section>
    </section>
</template>


<style scoped>
/* Container chung */
.health-corner-container {
    font-family: 'Arial', sans-serif;
    max-width: 1200px;
    margin: 0 auto;
}

/* --- Banner Styling --- */
.banner-wrapper {
    background: linear-gradient(90deg, #007bff 0%, #4facfe 100%);
    /* Màu xanh dương gradient */
    color: white;
    border-radius: 0 0 20px 20px;
    /* Bo tròn 2 góc dưới nếu muốn giống ảnh hoặc full width */
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
}

.banner-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 40px 20px 0 20px;
    /* Padding trên để chừa chỗ */
}

.banner-info {
    position: relative;
    flex: 1;
    z-index: 2;
}

/* Mô phỏng khung trắng bo tròn bao quanh chữ "Góc Sức Khỏe" */
.info-box {
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    padding: 20px;
    border-radius: 12px;
    display: inline-block;
    max-width: 400px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.info-box h2 {
    margin: 0 0 10px 0;
    color: #000;
    font-weight: bold;
}

.banner-image img {
    max-height: 300px;
    /* Điều chỉnh chiều cao ảnh bác sĩ */
    object-fit: contain;
    vertical-align: bottom;
    /* Để ảnh sát đáy */
}

/* Decor chữ thập nền */
.cross-bg-icon {
    position: absolute;
    top: -50px;
    left: 50px;
    font-size: 300px;
    font-weight: bold;
    color: rgba(255, 255, 255, 0.2);
    /* Màu trắng mờ */
    z-index: -1;
    line-height: 0.8;
}

/* --- Category Styling --- */
.category-wrapper {
    padding: 40px 20px;
}

.section-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
}

.category-grid {
    display: grid;
    /* Responsive: Tự động chia cột, tối thiểu 150px mỗi cột */
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 20px;
}

.category-card {
    background: white;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    border-color: #bdf;
}

.icon-circle {
    background: #f0f9ff;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    font-size: 24px;
}

.cat-name {
    margin: 5px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.cat-count {
    font-size: 12px;
    color: #888;
}

.post-section {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: 'Arial', sans-serif;
}

.section-title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
}

.mt-large {
    margin-top: 50px;
}

/* --- GRID SYSTEMS --- */
.featured-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    /* 2 cột đều nhau */
    gap: 30px;
}

.latest-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    /* 4 cột đều nhau */
    gap: 20px;
}

/* Responsive cho Mobile/Tablet */
@media (max-width: 992px) {
    .latest-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {

    .featured-grid,
    .latest-grid {
        grid-template-columns: 1fr;
    }

    /* Về 1 cột hết */
}

/* --- CARD STYLING --- */
.card-horizontal,
.card-vertical {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid #eee;
    transition: transform 0.2s;
    cursor: pointer;
}

.card-horizontal:hover,
.card-vertical:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Horizontal specific */
.card-horizontal {
    display: flex;
    flex-direction: row;
}

.card-horizontal .card-img-wrapper {
    width: 40%;
    flex-shrink: 0;
}

.card-horizontal .card-body {
    padding: 20px;
    width: 60%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Vertical specific */
.card-vertical {
    display: flex;
    flex-direction: column;
}

.card-vertical .card-img-wrapper {
    width: 100%;
    height: 180px;
    /* Cố định chiều cao ảnh */
    position: relative;
    /* Để đặt badge overlay */
}

.card-vertical .card-body {
    padding: 15px;
}

/* Common Image Style */
.card-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    /* Quan trọng: Giữ ảnh đẹp không bị méo */
}

/* Typo & Meta */
.card-title {
    font-size: 16px;
    font-weight: bold;
    margin: 10px 0;
    line-height: 1.4;
    /* Cắt dòng nếu tiêu đề quá dài (2 dòng) */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-desc {
    font-size: 13px;
    color: #666;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    /* Cắt dòng sau 3 dòng */
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-meta {
    font-size: 12px;
    color: #999;
    display: flex;
    gap: 15px;
    margin-top: auto;
    /* Đẩy xuống đáy */
}

/* Badge (Tags) */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
    width: fit-content;
    background: #eee;
}

.badge-overlay {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    color: white;
    font-weight: bold;
}

/* Màu sắc badge */
.green {
    background: #e6f9ed;
    color: #28a745;
}

.badge-overlay.green {
    background: #28a745;
    color: white;
}

.red {
    background: #fde8e8;
    color: #dc3545;
}

.badge-overlay.red {
    background: #dc3545;
    color: white;
}

.blue {
    background: #e8f0fe;
    color: #007bff;
}

.badge-overlay.blue {
    background: #007bff;
    color: white;
}

.orange {
    background: #fff3cd;
    color: #ffc107;
}

.badge-overlay.orange {
    background: #ffc107;
    color: black;
}

/* Button */
.action-area {
    text-align: center;
    margin-top: 40px;
}

.btn-load-more {
    background: #0066cc;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-load-more:hover {
    background: #0052a3;
}
</style>