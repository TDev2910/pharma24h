<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Global/Header.vue';
import { ref } from 'vue';

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    post: {
        type: Object,
        default: () => ({
            title: "5 Lợi ích của việc uống nước ấm mỗi ngày sau khi thức dậy",
            category: { name: "Sức khỏe" },
            author: { name: "BS. Nguyễn Văn A" },
            created_at: "2026-02-23",
            readTime: "6 phút đọc",
            image: "https://images.unsplash.com/photo-1544148103-0773bf10d330?q=80&w=1200&auto=format&fit=crop",
            summary: "Một thói quen đơn giản nhưng có khả năng thanh lọc cơ thể và kích hoạt hệ tiêu hóa hiệu quả nhất.",
            content: "<p>Nội dung chi tiết bài viết sẽ được hiển thị ở đây...</p>"
        })
    }
});

// Helper format ngày
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('vi-VN');
};
</script>

<template>

    <Head :title="post.title" />

    <div class="bg-[#fcfdfd] min-h-screen font-roboto text-gray-800 pb-20">
        <Header :auth="auth" />

        <div class="container mx-auto px-4 py-8 max-w-5xl">

            <div class="relative w-full h-[350px] md:h-[500px] rounded-2xl overflow-hidden shadow-sm"
                style="text-align: center;">

                <img :src="post.image || post.thumbnail" :alt="post.title"
                    class="absolute inset-0 w-full h-full object-cover">

                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                <div class="absolute bottom-8 left-6 md:bottom-12 md:left-10 pr-6 z-10 text-white max-w-4xl">
                    <span
                        class="inline-block bg-[#10b981] text-white text-xs font-bold px-3 py-1 rounded-md mb-4 uppercase tracking-wider">
                        {{ post.category?.name || post.category || 'Sức khỏe' }}
                    </span>
                    <h1
                        class="text-3xl md:text-4xl lg:text-5xl font-bold leading-snug font-display text-white drop-shadow-md">
                        {{ post.title }}
                    </h1>
                </div>
            </div>

            <!-- Content -->

            <!-- <div class="max-w-4xl mx-auto">

                <div v-if="post.summary" class="bg-[#ecfdf5] border-l-4 border-[#10b981] p-6 mb-10 rounded-r-xl">
                    <p class="text-[#064e3b] font-medium text-lg leading-relaxed italic">
                        {{ post.summary }}
                    </p>
                </div>

                <div class="article-content" v-html="post.content"></div>

            </div> -->

        </div>
    </div>
</template>

<style>
/* Font chữ */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

.font-roboto {
    font-family: 'Roboto', sans-serif;
}

.font-display {
    font-family: 'Playfair Display', serif;
}

/* Định dạng nội dung bài viết */
.article-content {
    font-size: 1.125rem;
    /* 18px */
    line-height: 1.85;
    color: #374151;
    /* gray-700 */
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.article-content ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}

.article-content img {
    width: 100%;
    border-radius: 12px;
    margin: 2rem 0;
}
</style>