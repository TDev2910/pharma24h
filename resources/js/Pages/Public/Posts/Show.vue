<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Global/Header.vue';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';

const tableOfContents = ref([]);
const activeSection = ref('');

const generateTOC = () => {
    const contentDiv = document.querySelector('.article-content');
    if (!contentDiv) return;

    // 1. Lấy tất cả các thẻ tiêu đề (thường là thẻ h2)
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
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

    <div class="bg-[#fcfdfd] min-h-screen text-gray-800 pb-20">

        <div class="container mx-auto px-4 pt-12 pb-8 max-w-5xl">
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
                    <h2 class="">Bài viết liên quan</h2>
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
@import "../../../../css/Public/Posts/show/show.css";
</style>