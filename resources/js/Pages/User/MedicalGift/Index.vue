<template>
    <div class="coming-soon-container">
        <div class="icon-wrapper">
            <i class="fas fa-hammer"></i>
        </div>
        <h2>Tính năng đang phát triển</h2>
        <p>Tính năng đổi quà đang trong quá trình hoàn thiện và sẽ sớm được ra mắt.</p>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.coming-soon-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.icon-wrapper {
    font-size: 4rem;
    color: #3b82f6;
    margin-bottom: 1.5rem;
    animation: bounce 2s infinite;
}

h2 {
    font-size: 1.5rem;
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

p {
    color: #6b7280;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background-color: #3b82f6;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-back:hover {
    background-color: #2563eb;
}

@keyframes bounce {

    0%,
    20%,
    50%,
    80%,
    100% {
        transform: translateY(0);
    }

    40% {
        transform: translateY(-20px);
    }

    60% {
        transform: translateY(-10px);
    }
}
</style>
