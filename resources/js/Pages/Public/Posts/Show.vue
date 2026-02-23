<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Global/Header.vue';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    post: { type: Object, required: true },
    relatedPosts: { type: Array, default: () => [] },
});

// Font size control
const fontSize = ref(16);
const increaseFont = () => { if (fontSize.value < 24) fontSize.value += 2; };
const decreaseFont = () => { if (fontSize.value > 14) fontSize.value -= 2; };
const resetFont = () => fontSize.value = 16;

</script>

<template>

    <Head :title="post.title" />

    <div class="bg-white min-h-screen font-roboto text-gray-800">
        <Header :auth="auth" />

        <div class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Breadcrumbs -->
            <div class="text-sm text-gray-500 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap">
                <Link href="/" class="hover:text-blue-600 transition">Trang chủ</Link>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <Link href="/posts" class="hover:text-blue-600 transition">Góc sức khỏe</Link>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-blue-600 font-medium cursor-default">{{ post.category }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                <!-- Main Content -->
                <div class="lg:col-span-8">

                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-6 font-display">
                        {{ post.title }}
                    </h1>
                    <!-- Summary Box -->
                    <div class="bg-gray-50 border-l-4 border-blue-600 p-6 mb-10 rounded-r-lg relative">
                        <i class="fas fa-quote-left text-blue-200 text-4xl absolute top-4 left-4 -z-10 opacity-50"></i>
                        <p class="text-gray-800 font-bold text-lg italic leading-relaxed z-10 relative">
                            {{ post.summary }}
                        </p>
                    </div>

                    <!-- Content -->
                    <div class="article-content" :style="{ fontSize: fontSize + 'px' }" v-html="post.content"></div>

                    <!-- Tags -->
                    <div v-if="post.tags && post.tags.length > 0" class="mt-12 pt-6 border-t border-gray-100">
                        <div class="flex flex-wrap gap-2">
                            <i class="fas fa-tags text-gray-400 mt-1.5"></i>
                            <span v-for="tag in post.tags" :key="tag"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm px-3 py-1 rounded-full transition cursor-pointer">
                                #{{ tag }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Related Posts -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden sticky top-8">
                        <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-bold text-gray-900 text-lg">Bài viết liên quan</h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <Link v-for="item in relatedPosts" :key="item.id" :href="`/bai-viet/${item.slug}`"
                                class="flex gap-4 p-4 hover:bg-blue-50/30 transition group">
                                <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                    <img :src="item.image"
                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                        :alt="item.title">
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-bold text-gray-800 group-hover:text-blue-600 line-clamp-2 leading-snug mb-2">
                                        {{ item.title }}
                                    </h4>
                                    <span class="text-xs text-gray-400 group-hover:text-blue-500 transition"><i
                                            class="far fa-clock mr-1"></i> {{ item.date }}</span>
                                </div>
                            </Link>
                        </div>
                        <div v-if="relatedPosts.length === 0" class="p-4 text-center text-gray-400 text-sm">
                            Không có bài viết liên quan.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

.font-roboto {
    font-family: 'Roboto', sans-serif;
}

.font-display {
    font-family: 'Playfair Display', serif;
}

/* Article Content Styles */
.article-content {
    color: #1f2937;
    /* gray-800 */
    line-height: 1.8;
}

.article-content p {
    margin-bottom: 1.5em;
}

.article-content h2 {
    font-size: 1.75em;
    font-weight: 700;
    color: #111827;
    /* gray-900 */
    margin-top: 1.5em;
    margin-bottom: 0.75em;
    line-height: 1.3;
}

.article-content h3 {
    font-size: 1.5em;
    font-weight: 600;
    color: #1f2937;
    margin-top: 1.5em;
    margin-bottom: 0.75em;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1.5em;
    padding-left: 1.5em;
    list-style-type: disc;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 2em 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.article-content blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1.25em;
    font-style: italic;
    color: #4b5563;
    margin: 1.5em 0;
}
</style>