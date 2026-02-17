<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    categories: { type: Array, default: () => [] },
    featuredPosts: { type: Array, default: () => [] },
    categorySections: { type: Array, default: () => [] }, // New prop from backend
    filters: { type: Object, default: () => ({}) },
});

// --- HELPER: Map Category Slug to Icon/Color ---
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

// Category Sections (Processed with colors/meta)
const processedCategorySections = computed(() => {
    return props.categorySections.map(section => {
        const meta = getCategoryMeta(section.slug);
        return {
            ...section,
            color: meta.color,
            posts: section.posts.map(post => ({
                ...post,
                desc: post.summary,
                catColor: meta.color
            }))
        };
    });
});

// --- ACTIONS ---
const handleCategoryClick = (category) => {
    router.get('/posts', { category: category.slug }, { preserveState: true, preserveScroll: true });
};

const loadMore = () => {
    console.log('Load more');
};
</script>

<template>

    <Head title="Góc sức khỏe" />

    <div class="contact-banner" style="margin-top: 80px;">
        <div class="container">
            <h1 class="fw-bold text-primary-dark">Góc sức khỏe</h1>
            <p class="text-secondary">Hãy tìm hiểu thông tin y tế, sức khỏe và các thông tin liên quan đến sức khỏe.</p>
        </div>
    </div>
    <section class="health-corner-container">
        <!-- các bài viết nổi bật (HERO SECTION) -->
        <section class="post-section">
            <h3 class="section-title">Bài viết nổi bật</h3>

            <div v-if="processedFeaturedPosts.length > 0" class="hero-layout mb-5">
                <!-- Main Hero Post (Left) -->
                <div class="hero-main" v-if="processedFeaturedPosts[0]">
                    <div class="hero-img-wrapper">
                        <img :src="processedFeaturedPosts[0].image" :alt="processedFeaturedPosts[0].title">
                    </div>
                    <div class="hero-body">
                        <h2 class="hero-title">{{ processedFeaturedPosts[0].title }}</h2>
                        <p class="hero-desc d-none d-md-block">{{ processedFeaturedPosts[0].desc }}</p>
                    </div>
                </div>

                <!-- Side Posts List (Right) -->
                <div class="hero-side">
                    <div v-for="post in processedFeaturedPosts.slice(1, 6)" :key="post.id" class="side-item">
                        <div class="side-img-wrapper">
                            <img :src="post.image" :alt="post.title">
                        </div>
                        <div class="side-body">
                            <h4 class="side-title">{{ post.title }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-4 text-gray-500">
                Chưa có bài viết nổi bật.
            </div>

            <!-- NEW LAYOUT: Content + Sidebar -->
            <div class="content-layout">
                <!-- LEFT COLUMN: Categories & Posts -->
                <div class="main-content">
                    <div v-if="processedCategorySections.length > 0">
                        <div v-for="group in processedCategorySections" :key="group.id" class="category-block mb-5">
                            <div class="block-header">
                                <h3 class="block-title" :class="group.color">{{ group.name }}</h3>
                                <div class="block-links d-none d-md-flex">
                                    <span class="sub-link-mock">Kiến thức y khoa</span>
                                    <span class="sub-link-mock">Sức khỏe gia đình</span>
                                </div>
                                <a href="#" class="view-all"
                                    @click.prevent="handleCategoryClick({ slug: group.slug })">Xem tất cả <i
                                        class="fas fa-chevron-right"></i></a>
                            </div>

                            <div class="block-grid">
                                <!-- First Post: Large -->
                                <div v-if="group.posts[0]" class="post-large">
                                    <div class="img-wrapper">
                                        <img :src="group.posts[0].image" :alt="group.posts[0].title">
                                    </div>
                                    <h4 class="post-title-lg">{{ group.posts[0].title }}</h4>
                                    <p class="post-desc">{{ group.posts[0].desc }}</p>
                                </div>

                                <!-- Second Post: Large (if exists) or List -->
                                <div v-if="group.posts[1]" class="post-medium">
                                    <h4 class="post-title-md">{{ group.posts[1].title }}</h4>
                                    <p class="post-desc">{{ group.posts[1].desc }}</p>
                                </div>

                                <!-- Remaining posts: Small list below -->
                                <div v-if="group.posts.length > 2" class="post-list-row">
                                    <div v-for="post in group.posts.slice(2, 5)" :key="post.id" class="post-small">
                                        <h5 class="post-title-sm">{{ post.title }}</h5>
                                        <p class="post-desc-sm d-none d-lg-block">{{ post.desc }}</p>
                                    </div>
                                </div>
                            </div>
                            <hr class="section-divider" />
                        </div>
                    </div>
                    <div v-else class="text-center py-4 text-gray-500">
                        Chưa có bài viết mới.
                    </div>
                </div>

                <!-- RIGHT COLUMN: Sidebar Categories -->
                <aside class="sidebar-content">
                    <div class="sidebar-box">
                        <h3 class="sidebar-title">Chuyên đề nổi bật</h3>
                        <div class="topic-list">
                            <div v-for="cat in processedCategories" :key="cat.id" class="topic-item"
                                @click="handleCategoryClick(cat)">
                                <div class="topic-name"># {{ cat.name }}</div>
                                <div class="topic-count">{{ cat.count }} bài viết</div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <a href="#" class="view-all-topics">Xem tất cả <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="action-area">
                <button @click="loadMore" class="btn-load-more">Xem thêm bài viết</button>
            </div>
        </section>
    </section>
</template>


<style scoped>
.contact-banner {
    background: linear-gradient(180deg, #E6F3FF 0%, #FFFFFF 100%);
    padding: 60px 0 40px;
    border-bottom: 1px solid #f0f0f0;
}

/* Container chung */
.health-corner-container {
    font-family: 'Arial', sans-serif;
    max-width: 1200px;
    margin: 0 auto;
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

/* --- HERO LAYOUT STYLES --- */
.hero-layout {
    display: grid;
    grid-template-columns: 1.8fr 1.2fr;
    /* Chia tỉ lệ ~60/40 hoặc 65/35 */
    gap: 30px;
}

.hero-main {
    display: flex;
    flex-direction: column;
}

.hero-img-wrapper {
    width: 100%;
    height: 350px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 15px;
}

.hero-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.3;
}

.hero-desc {
    font-size: 15px;
    color: #555;
    line-height: 1.5;
}

.hero-side {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.side-item {
    display: flex;
    gap: 15px;
    align-items: flex-start;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.side-item:last-child {
    border-bottom: none;
}

.side-img-wrapper {
    width: 120px;
    height: 80px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
}

.side-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.side-title {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.side-title:hover {
    color: #007bff;
}

@media (max-width: 768px) {
    .hero-layout {
        grid-template-columns: 1fr;
    }

    .hero-img-wrapper {
        height: 250px;
    }

    .side-item {
        align-items: center;
    }
}

/* --- CARD STYLING --- */
.card-horizontal {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid #eee;
    transition: transform 0.2s;
    cursor: pointer;
}

.card-horizontal:hover {
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

/* Màu sắc badge */
.green {
    background: #e6f9ed;
    color: #28a745;
}

.red {
    background: #fde8e8;
    color: #dc3545;
}

.blue {
    background: #e8f0fe;
    color: #007bff;
}

.orange {
    background: #fff3cd;
    color: #ffc107;
}

.purple {
    background: #f3e5f5;
    color: #9c27b0;
}

.pink {
    background: #fce4ec;
    color: #e91e63;
}

/* === NEW LAYOUT STYLES === */
.content-layout {
    display: flex;
    gap: 30px;
}

.main-content {
    flex: 3;
    /* 75% */
}

.sidebar-content {
    flex: 1;
    /* 25% */
    min-width: 250px;
}

/* Category Block */
.category-block {
    margin-bottom: 40px;
}

.block-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.block-title {
    font-size: 18px;
    font-weight: bold;
    color: #0056b3;
    /* Default blue */
    margin: 0;
}

/* Dynamic title colors text */
.block-title.red {
    color: #dc3545;
}

.block-title.green {
    color: #28a745;
}

.block-title.blue {
    color: #007bff;
}

.block-title.orange {
    color: #ffc107;
}

.block-title.purple {
    color: #9c27b0;
}

.block-title.pink {
    color: #e91e63;
}


.block-links {
    display: flex;
    gap: 15px;
}

.sub-link {
    font-size: 13px;
    color: #666;
    text-decoration: none;
}

.sub-link:hover {
    color: #007bff;
}

.view-all {
    font-size: 13px;
    color: #007bff;
    text-decoration: none;
    font-weight: 600;
}

.block-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.post-large .img-wrapper {
    width: 100%;
    height: 200px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
}

.post-large .img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.post-title-lg {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.post-title-md {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
}

.post-desc {
    font-size: 14px;
    color: #555;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.post-list-row {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    padding-top: 20px;
    border-top: 1px dashed #eee;
}

.post-small {
    cursor: pointer;
}

.post-small:hover .post-title-sm {
    color: #007bff;
}

.post-title-sm {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 5px;
    transition: color 0.2s;
}

.post-desc-sm {
    font-size: 12px;
    color: #777;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.section-divider {
    border: 0;
    border-top: 1px solid #eee;
    margin: 30px 0;
}

/* Sidebar Box */
.sidebar-box {
    background: #eef3fc;
    border-radius: 12px;
    padding: 20px;
}

.sidebar-title {
    font-size: 18px;
    font-weight: bold;
    color: #0056b3;
    margin-bottom: 20px;
}

.topic-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.topic-item {
    padding: 10px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    cursor: pointer;
    transition: background-color 0.2s;
}

.topic-item:last-child {
    border-bottom: none;
}

.topic-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
    border-radius: 4px;
}

.topic-item:hover .topic-name {
    color: #007bff;
}

.topic-name {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    margin-bottom: 2px;
    transition: color 0.2s;
}

.topic-count {
    font-size: 12px;
    color: #888;
}

.view-all-topics {
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    color: #007bff;
}

.view-all-topics:hover {
    text-decoration: underline;
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

/* Responsive */
@media (max-width: 991px) {
    .content-layout {
        flex-direction: column;
    }

    .sidebar-content {
        order: 1;
        /* Sidebar below main content on smaller screens */
    }

    .block-links {
        display: none;
        /* Hide sub-links on smaller screens */
    }
}

@media (max-width: 768px) {
    .block-grid {
        grid-template-columns: 1fr;
        /* Single column for block grid */
    }

    .post-list-row {
        grid-template-columns: 1fr;
        /* Single column for post list row */
    }
}
</style>