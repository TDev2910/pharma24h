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
        'tim-mach': { icon: '❤️', color: 'blue' },
        'dinh-duong': { icon: '🍏', color: 'blue' },
        'thuoc-dieu-tri': { icon: '💊', color: 'blue' },
        'giac-ngu': { icon: '🌙', color: 'blue' },
        'kham-benh': { icon: '🩺', color: 'blue' },
        'thao-duoc': { icon: '🍃', color: 'blue' },
        'lam-dep': { icon: '💄', color: 'blue' },
        'me-va-be': { icon: '👶', color: 'blue' },
        'covid-19': { icon: '🦠', color: 'blue' },
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

            <!-- Content + Sidebar -->
            <div class="content-layout">
                <!-- LEFT COLUMN: Categories & Posts -->
                <div class="main-content">
                    <div v-if="processedCategorySections.length > 0">
                        <div v-for="group in processedCategorySections" :key="group.id" class="category-block mb-5">
                            <div
                                class="block-header d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                <div class="d-flex align-items-center">
                                    <Link :href="`/posts?category=${group.slug}`" class="text-decoration-none">
                                        <h3 class="block-title group-hover-primary" :class="group.color"
                                            style="font-size: 22px; font-weight: 700; margin-bottom: 0;">{{ group.name
                                            }}
                                        </h3>
                                    </Link>
                                    <div class="block-links d-none d-lg-flex ms-3 ps-3 border-start">
                                        <template v-for="(sub, sIndex) in group.subcategories" :key="sIndex">
                                            <Link :href="`/posts?category=${sub.slug}`"
                                                class="text-secondary text-decoration-none hover-primary small">
                                                {{ sub.name }}
                                            </Link>
                                            <span v-if="sIndex < group.subcategories.length - 1"
                                                class="mx-2 text-muted opacity-50">|</span>
                                        </template>
                                    </div>
                                </div>
                                <Link :href="`/posts?category=${group.slug}`"
                                    class="text-primary text-decoration-none fw-bold small">
                                    Xem tất cả <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                                </Link>
                            </div>

                            <div class="block-content">
                                <!-- Row 1: Featured + Secondary -->
                                <div class="row g-4 mb-4">
                                    <div class="col-lg-8" v-if="group.posts[0]">
                                        <Link :href="`/bai-viet/${group.posts[0].slug}`"
                                            class="text-decoration-none article-group d-flex gap-4">
                                            <div class="flex-shrink-0 featured-img-wrapper"
                                                style="width: 320px; height: 200px; border-radius: 12px; overflow: hidden;">
                                                <img :src="group.posts[0].image"
                                                    class="w-100 h-100 object-cover transition-transform duration-500"
                                                    :alt="group.posts[0].title">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h4
                                                    class="fs-4 fw-bold text-dark group-hover-primary transition-colors line-clamp-2 mb-2">
                                                    {{
                                                        group.posts[0].title }}</h4>
                                                <p class="text-secondary small line-clamp-4 leading-relaxed mb-0">{{
                                                    group.posts[0].desc }}</p>
                                            </div>
                                        </Link>
                                    </div>
                                    <div class="col-lg-4 border-start" v-if="group.posts[1]">
                                        <Link :href="`/bai-viet/${group.posts[1].slug}`"
                                            class="text-decoration-none article-group h-100 d-block">
                                            <h4
                                                class="fs-5 fw-bold text-dark group-hover-primary transition-colors line-clamp-2 mb-2">
                                                {{
                                                    group.posts[1].title }}</h4>
                                            <p class="text-secondary small line-clamp-4 leading-relaxed mb-0">{{
                                                group.posts[1].desc }}</p>
                                        </Link>
                                    </div>
                                </div>

                                <!-- Row 2: Three columns -->
                                <div class="row g-4 pt-4 border-top" v-if="group.posts.length > 2">
                                    <div v-for="post in group.posts.slice(2, 5)" :key="post.id" class="col-md-4">
                                        <Link :href="`/bai-viet/${post.slug}`"
                                            class="text-decoration-none article-group">
                                            <h4
                                                class="fs-6 fw-bold text-dark group-hover-primary transition-colors line-clamp-2 mb-2">
                                                {{ post.title }}
                                            </h4>
                                            <p class="text-secondary smaller line-clamp-3 leading-relaxed mb-0">{{
                                                post.desc }}</p>
                                        </Link>
                                    </div>
                                </div>
                            </div>
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


<style scoped>
@import "../../../../css/Public/Posts/index/index.css";
</style>