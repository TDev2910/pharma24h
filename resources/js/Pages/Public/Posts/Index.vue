<script setup>
import { computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    categories: { type: Array, default: () => [] },
    featuredPosts: { type: Array, default: () => [] },
    categorySections: { type: Array, default: () => [] },
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
        <section class="post-section">
            <h3 class="section-title">Bài viết nổi bật</h3>

            <div v-if="processedFeaturedPosts.length > 0" class="hero-layout mb-5">
                <Link v-if="processedFeaturedPosts[0]" :href="`/bai-viet/${processedFeaturedPosts[0].slug}`"
                    class="hero-main cursor-pointer block text-decoration-none">
                    <div class="hero-img-wrapper">
                        <img :src="processedFeaturedPosts[0].image" :alt="processedFeaturedPosts[0].title">
                    </div>
                    <div class="hero-body">
                        <h2 class="hero-title text-dark">{{ processedFeaturedPosts[0].title }}</h2>
                        <p class="hero-desc d-none d-md-block text-secondary">{{ processedFeaturedPosts[0].desc }}</p>
                    </div>
                </Link>

                <!-- Side Posts List (Right) -->
                <div class="hero-side">
                    <Link v-for="post in processedFeaturedPosts.slice(1, 6)" :key="post.id"
                        :href="`/bai-viet/${post.slug}`" class="side-item cursor-pointer block text-decoration-none">
                        <div class="side-img-wrapper">
                            <img :src="post.image" :alt="post.title">
                        </div>
                        <div class="side-body">
                            <h4 class="side-title text-dark">{{ post.title }}</h4>
                        </div>
                    </Link>
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
                                <Link v-if="group.posts[0]" :href="`/bai-viet/${group.posts[0].slug}`"
                                    class="post-large cursor-pointer block text-decoration-none">
                                    <div class="img-wrapper">
                                        <img :src="group.posts[0].image" :alt="group.posts[0].title">
                                    </div>
                                    <h4 class="post-title-lg text-dark">{{ group.posts[0].title }}</h4>
                                    <p class="post-desc text-secondary">{{ group.posts[0].desc }}</p>
                                </Link>

                                <!-- Second Post: Large (if exists) or List -->
                                <Link v-if="group.posts[1]" :href="`/bai-viet/${group.posts[1].slug}`"
                                    class="post-medium cursor-pointer block text-decoration-none">
                                    <h4 class="post-title-md text-dark">{{ group.posts[1].title }}</h4>
                                    <p class="post-desc text-secondary">{{ group.posts[1].desc }}</p>
                                </Link>

                                <!-- Remaining posts: Small list below -->
                                <div v-if="group.posts.length > 2" class="post-list-row">
                                    <Link v-for="post in group.posts.slice(2, 5)" :key="post.id"
                                        :href="`/bai-viet/${post.slug}`"
                                        class="post-small cursor-pointer block text-decoration-none">
                                        <h5 class="post-title-sm text-dark">{{ post.title }}</h5>
                                        <p class="post-desc-sm d-none d-lg-block text-secondary">{{ post.desc }}</p>
                                    </Link>
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
                            <div v-for="cat in processedCategories" :key="cat.id" class="topic-item cursor-pointer"
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


<style src="../../../../css/Public/Posts/index.css"></style>