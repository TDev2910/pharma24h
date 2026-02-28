<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Global/Header.vue';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';

const tableOfContents = ref([]);
const activeSection = ref('');

const generateTOC = () => {
    const contentDiv = document.querySelector('.article-content');
    if (!contentDiv) return;

    const headings = contentDiv.querySelectorAll('h2');
    tableOfContents.value = [];

    headings.forEach((heading, index) => {
        const id = heading.id || `heading-${index}`;
        heading.id = id;
        tableOfContents.value.push({
            id: id,
            text: heading.innerText
        });
    });

    if (tableOfContents.value.length > 0) {
        activeSection.value = tableOfContents.value[0].id;
    }
};

const scrollToSection = (id) => {
    const element = document.getElementById(id);
    if (element) {
        const y = element.getBoundingClientRect().top + window.scrollY - 100;
        window.scrollTo({ top: y, behavior: 'smooth' });
    }
};

const onScroll = () => {
    const headings = tableOfContents.value.map(item => document.getElementById(item.id));
    let currentId = activeSection.value;

    for (const heading of headings) {
        if (heading) {
            const rect = heading.getBoundingClientRect();
            if (rect.top <= window.innerHeight / 3) {
                currentId = heading.id;
            }
        }
    }
    activeSection.value = currentId;
};

onMounted(() => {
    nextTick(() => {
        setTimeout(() => {
            generateTOC();
        }, 100);
    });
    window.addEventListener('scroll', onScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});

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
            content: "<h2>Lợi ích của nước ấm</h2><p>Uống nước ấm vào buổi sáng là một trong những thói quen đơn giản nhất mà bạn có thể thực hiện để cải thiện sức khỏe tổng thể. Dưới đây là 5 lợi ích chính:</p><ul><li><strong>Hỗ trợ tiêu hóa:</strong> Giúp phân hủy thức ăn nhanh hơn và giảm nguy cơ táo bón.</li><li><strong>Thanh lọc độc tố:</strong> Kích thích hệ bạch huyết và đào thải chất thừa ra khỏi cơ thể qua đường bài tiết.</li></ul><h2>Thời điểm uống tốt nhất</h2><p>Thời điểm lý tưởng nhất để uống nước ấm là ngay sau khi thức dậy, trước khi ăn sáng khoảng 30 phút.</p>",
            // Thêm mảng tags để làm giao diện
            tags: ["Nước ấm", "Sức khỏe", "Thói quen tốt", "Tiêu hóa", "Detox"]
        })
    },
    // Thêm dummy data cho bài viết liên quan để test giao diện
    relatedPosts: {
        type: Array,
        default: () => [
            {
                id: 1,
                title: "10 thói quen ăn sáng lành mạnh giúp tăng cường sức khỏe",
                slug: "10-thoi-quen-an-sang",
                category: "Dinh dưỡng",
                image: "https://images.unsplash.com/photo-1490474418585-ba9ca8a56d62?q=80&w=800&auto=format&fit=crop",
                summary: "Bữa sáng là bữa ăn quan trọng nhất trong ngày. Hãy khám phá những món ăn giúp bạn nạp năng lượng...",
                date: "20/02/2026",
                readTime: "5 phút đọc"
            },
            {
                id: 2,
                title: "Yoga buổi sáng: 7 bài tập đơn giản cho người mới bắt đầu",
                slug: "yoga-buoi-sang",
                category: "Thể dục",
                image: "https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=800&auto=format&fit=crop",
                summary: "Bắt đầu ngày mới với những bài tập yoga nhẹ nhàng giúp cơ thể tràn đầy năng lượng và dẻo dai hơn.",
                date: "18/02/2026",
                readTime: "7 phút đọc"
            },
            {
                id: 3,
                title: "Các loại trà thảo mộc tốt cho hệ tiêu hóa và giấc ngủ",
                slug: "tra-thao-moc",
                category: "Sức khỏe",
                image: "https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?q=80&w=800&auto=format&fit=crop",
                summary: "Trà thảo mộc không chỉ thơm ngon mà còn mang lại nhiều lợi ích sức khỏe tuyệt vời nếu bạn dùng đúng cách.",
                date: "15/02/2026",
                readTime: "4 phút đọc"
            }
        ]
    }
});

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
                <div class="absolute bottom-8 left-6 md:bottom-12 md:left-10 pr-6 z-10 text-white max-w-4xl text-left">
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
            <div class="toc-layout">

                <!-- Nội dung chính -->
                <div class="toc-main">

                    <!-- Khung trắng bao bầu nội dung -->
                    <div class="toc-content-box">

                        <div v-if="post.summary" class="toc-summary">
                            <p class="toc-summary-text">{{ post.summary }}</p>
                        </div>

                        <div class="article-content" v-html="post.content"></div>

                    </div>

                    <!-- Tags -->
                    <div v-if="post.tags && post.tags.length > 0" class="mt-10 flex flex-wrap gap-3">
                        <Link v-for="tag in post.tags" :key="tag" :href="`/tags/${tag}`"
                            class="px-4 py-2 bg-[#ecfdf5] text-[#065f46] hover:bg-[#10b981] hover:text-white rounded-full text-sm font-medium transition-colors duration-300">
                            {{ tag }}
                        </Link>
                    </div>

                </div>

                <!-- Mục lục bên phải -->
                <aside class="toc-sidebar">
                    <div class="toc-box">
                        <p class="toc-title">Mục lục</p>
                        <nav>
                            <ul class="toc-list">
                                <li v-for="item in tableOfContents" :key="item.id">
                                    <button @click="scrollToSection(item.id)" class="toc-link"
                                        :class="{ 'toc-link--active': activeSection === item.id }">
                                        {{ item.text }}
                                    </button>
                                </li>
                            </ul>
                            <div v-if="tableOfContents.length === 0" class="toc-empty">
                                Đang tải mục lục...
                            </div>
                        </nav>
                    </div>
                </aside>

            </div>

            <!-- Bài viết liên quan -->
            <div v-if="relatedPosts && relatedPosts.length > 0" class="related-section">

                <div class="related-header">
                    <div class="related-bar"></div>
                    <h2 class="related-heading">Bài viết liên quan</h2>
                </div>

                <div class="related-grid">
                    <Link v-for="related in relatedPosts" :key="related.id" :href="`/posts/${related.slug}`"
                        class="related-item">
                        <div class="related-img-wrap">
                            <img :src="related.image" :alt="related.title" class="related-img">
                            <span class="related-badge">{{ related.category }}</span>
                        </div>
                        <div class="related-body">
                            <h3 class="related-item-title">{{ related.title }}</h3>
                            <p class="related-item-summary">{{ related.summary }}</p>
                            <div class="related-meta">
                                <span>{{ related.date }}</span>
                                <span class="related-meta-dot"></span>
                                <span class="related-meta-time">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="related-clock-icon" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ related.readTime }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

            </div>

        </div>
    </div>
</template>

<style>
/* CSS không đổi so với code chuẩn trước của bạn */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

.font-roboto {
    font-family: 'Roboto', sans-serif;
}

.font-display {
    font-family: 'Playfair Display', serif;
}

.article-content {
    font-size: 1.125rem;
    line-height: 1.85;
    color: #374151;
    width: 100%;
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
}

.article-content * {
    max-width: 100% !important;
    white-space: normal !important;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content h2 {
    font-size: 1.65rem;
    font-weight: 700;
    color: #111827;
    margin-top: 2.5rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.article-content h3 {
    font-size: 1.35rem;
    font-weight: 600;
    color: #1f2937;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.article-content ul {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 1.5rem;
}

.article-content ul li {
    position: relative;
    padding-left: 1.5rem;
    margin-bottom: 0.85rem;
}

.article-content ul li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.65rem;
    width: 6px;
    height: 6px;
    background-color: #10b981;
    border-radius: 50%;
}

.article-content strong {
    color: #111827;
    font-weight: 600;
}

.article-content img {
    width: 100%;
    border-radius: 12px;
    margin: 2rem 0;
}

/* ==============================
   TOC LAYOUT - 2 CỘT
   ============================== */
.toc-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
    margin-top: 2rem;
    align-items: start;
}

@media (min-width: 1024px) {
    .toc-layout {
        grid-template-columns: 1fr 230px;
        gap: 2.5rem;
    }
}

.toc-main {
    min-width: 0;
}

.toc-content-box {
    background: #ffffff;
    border: 1px solid #e9ebee;
    border-radius: 16px;
    padding: 2rem 2.25rem;
}

.toc-summary {
    background-color: #ecfdf5;
    border-left: 4px solid #10b981;
    padding: 1.1rem 1.4rem;
    margin-bottom: 2rem;
    border-radius: 0 10px 10px 0;
}

.toc-summary-text {
    color: #064e3b;
    font-weight: 500;
    font-size: 1rem;
    line-height: 1.75;
    font-style: italic;
    margin: 0;
}

/* ==============================
   SIDEBAR MỤC LỤC
   ============================== */
.toc-sidebar {
    display: none;
}

@media (min-width: 1024px) {
    .toc-sidebar {
        display: block;
    }
}

.toc-box {
    position: sticky;
    top: 5.5rem;
    background: #ffffff;
    border: 1px solid #e9ebee;
    border-radius: 14px;
    padding: 1.25rem 1.25rem 1.5rem;
    overflow: hidden;
    max-width: 100%;
}

.toc-title {
    font-size: 0.68rem;
    font-weight: 800;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    margin: 0 0 0.85rem;
}

.toc-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.toc-link {
    display: block;
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.85rem;
    color: #6b7280;
    padding: 0.55rem 0.75rem;
    border-radius: 8px;
    line-height: 1.45;
    transition: background 0.18s ease, color 0.18s ease;
    /* Cắt chữ bằng ... khi quá dài */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    box-sizing: border-box;
    min-width: 0;
}

.toc-link:hover {
    background-color: #f9fafb;
    color: #1f2937;
}

.toc-link--active {
    background-color: #ecfdf5;
    color: #10b981 !important;
    font-weight: 600;
}

.toc-empty {
    font-size: 0.8rem;
    color: #9ca3af;
    font-style: italic;
    padding: 0.25rem 0.75rem;
}

/* ==============================
   BÀI VIẾT LIÊN QUAN
   ============================== */
.related-section {
    margin-top: 4.5rem;
    padding-top: 2.5rem;
    border-top: 2px solid #f3f4f6;
}

.related-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 2rem;
}

.related-bar {
    width: 5px;
    height: 28px;
    background-color: #10b981;
    border-radius: 3px;
    flex-shrink: 0;
}

.related-heading {
    font-size: 1.4rem;
    font-weight: 800;
    color: #111827;
    margin: 0;
    font-family: 'Playfair Display', serif;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

@media (max-width: 1023px) {
    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 639px) {
    .related-grid {
        grid-template-columns: 1fr;
    }
}

.related-item {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.07);
    text-decoration: none;
    transition: transform 0.28s ease, box-shadow 0.28s ease;
}

.related-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.10);
}

.related-img-wrap {
    position: relative;
    width: 100%;
    padding-top: 56%;
    overflow: hidden;
}

.related-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.related-item:hover .related-img {
    transform: scale(1.06);
}

.related-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: #10b981;
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 4px 11px;
    border-radius: 999px;
    letter-spacing: 0.02em;
    text-transform: capitalize;
    white-space: nowrap;
}

.related-body {
    padding: 1.1rem 1.1rem 1.4rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.related-item-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a2e;
    line-height: 1.45;
    margin: 0 0 0.55rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}

.related-item:hover .related-item-title {
    color: #10b981;
}

.related-item-summary {
    font-size: 0.825rem;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 1rem;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-meta {
    display: flex;
    align-items: center;
    font-size: 0.72rem;
    color: #9ca3af;
    gap: 6px;
    margin-top: auto;
}

.related-meta-dot {
    width: 4px;
    height: 4px;
    background-color: #d1d5db;
    border-radius: 50%;
    flex-shrink: 0;
}

.related-meta-time {
    display: flex;
    align-items: center;
    gap: 4px;
}

.related-clock-icon {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
}
</style>