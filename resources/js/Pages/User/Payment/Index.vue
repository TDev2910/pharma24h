<template>
    <div class="payment-container">
        <div class="page-header">
            <h1>Thanh toán & Ví</h1>
            <p class="text-subtitle">Quản lý phương thức thanh toán và lịch sử giao dịch</p>
        </div>

        <!-- Stats Cards -->
        <div class="payment-stats">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Số dư ví</span>
                    <span class="stat-value">0 ₫</span>
                </div>
                <button class="action-btn">Nạp tiền</button>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Công nợ</span>
                    <span class="stat-value">{{ formatCurrency(debtAmount) }} ₫</span>
                </div>
                <button class="action-btn outline">Thanh toán</button>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Điểm thưởng</span>
                    <span class="stat-value">1,250</span> <!-- Mocked -->
                </div>
                <Link href="/user/rewards" class="action-link">Đổi quà <i class="fas fa-arrow-right"></i></Link>
            </div>
        </div>

        <div class="content-grid">
            <!-- Transaction History (Left) -->
            <div class="transaction-section">
                <div class="section-header">
                    <h3><i class="fas fa-history"></i> Lịch sử giao dịch</h3>
                    <div class="filter-actions">
                        <select class="form-select">
                            <option>Tất cả</option>
                            <option>Nạp tiền</option>
                            <option>Thanh toán đơn hàng</option>
                        </select>
                    </div>
                </div>

                <div class="transaction-list">
                    <div v-if="transactions.length === 0" class="empty-state">
                        <div class="empty-icon-wrapper">
                            <i class="far fa-file-alt"></i>
                        </div>
                        <p>Chưa có giao dịch nào gần đây</p>
                    </div>

                    <div v-else v-for="tx in transactions" :key="tx.id" class="transaction-item">
                        <div class="tx-icon" :class="tx.type">
                            <i :class="getTxIcon(tx.type)"></i>
                        </div>
                        <div class="tx-info">
                            <span class="tx-title">{{ tx.description }}</span>
                            <span class="tx-date">{{ tx.date }}</span>
                        </div>
                        <div class="tx-amount" :class="tx.amount > 0 ? 'positive' : 'negative'">
                            {{ tx.amount > 0 ? '+' : '' }}{{ formatCurrency(tx.amount) }} ₫
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Payment Methods -->
                <div class="section-card">
                    <div class="section-header">
                        <h3><i class="fas fa-credit-card"></i> Phương thức thanh toán</h3>
                        <button class="btn-add"><i class="fas fa-plus"></i> Thêm mới</button>
                    </div>

                    <div class="cards-list">
                        <div class="empty-method">
                            <p>Bạn chưa thêm thẻ ngân hàng nào.</p>
                        </div>
                    </div>
                </div>

                <!-- E-Wallets -->
                <div class="section-card">
                    <div class="section-header">
                        <h3><i class="fas fa-qrcode"></i> Ví điện tử liên kết</h3>
                    </div>
                    <div class="wallets-list">
                        <div class="wallet-item">
                            <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="Momo"
                                class="wallet-logo">
                            <div class="wallet-info">
                                <span class="w-name">MoMo</span>
                                <span class="w-status not-linked">Chưa liên kết</span>
                            </div>
                            <button class="btn-link">Liên kết</button>
                        </div>
                        <div class="wallet-item">
                            <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/6/0oxhzjmxbksr1686814746013.png"
                                alt="VNPAY" class="wallet-logo">
                            <div class="wallet-info">
                                <span class="w-name">VNPAY</span>
                                <span class="w-status not-linked">Chưa liên kết</span>
                            </div>
                            <button class="btn-link">Liên kết</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    user: Object,
    debtAmount: {
        type: Number,
        default: 0
    },
    transactions: {
        type: Array,
        default: () => []
    }
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('vi-VN').format(value)
}

const getTxIcon = (type) => {
    if (type === 'deposit') return 'fas fa-arrow-down'
    if (type === 'payment') return 'fas fa-shopping-cart'
    return 'fas fa-exchange-alt'
}
</script>

<style scoped>
.payment-container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 24px;
}

.page-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px 0;
}

.text-subtitle {
    color: #64748b;
    font-size: 14px;
}

/* Stats Cards */
.payment-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
}

.stat-card.blue {
    border-left: 4px solid #3b82f6;
}

.stat-card.orange {
    border-left: 4px solid #f97316;
}

.stat-card.purple {
    border-left: 4px solid #a855f7;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.blue .stat-icon {
    background: #eff6ff;
    color: #3b82f6;
}

.orange .stat-icon {
    background: #fff7ed;
    color: #f97316;
}

.purple .stat-icon {
    background: #faf5ff;
    color: #a855f7;
}

.stat-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    background: #1e293b;
    color: white;
    transition: all 0.2s;
}

.action-btn.outline {
    background: transparent;
    border: 1px solid #cbd5e1;
    color: #475569;
}

.action-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.action-link {
    font-size: 13px;
    color: #a855f7;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Grid Layout */
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

/* Sections */
/* Sections */
.transaction-section,
.section-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e2e8f0;
    /* Clean border, no shadow for flat design or subtle shadow */
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.right-column {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.section-header h3 i {
    color: #64748b;
}

/* Form Select */
.form-select {
    padding: 6px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 13px;
    color: #475569;
    background-color: white;
    cursor: pointer;
    outline: none;
}

.form-select:hover {
    border-color: #cbd5e1;
}

/* Transactions List */
.transaction-list {
    min-height: 200px;
    /* Ensure some height for empty state */
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 0;
    color: #94a3b8;
    height: 100%;
}

.empty-icon-wrapper {
    width: 64px;
    height: 64px;
    background: #f1f5f9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.empty-img {
    width: 120px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.transaction-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    transition: all 0.2s;
}

.transaction-item:hover {
    background: #f8fafc;
}

.tx-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.tx-icon.deposit {
    background: #dcfce7;
    color: #16a34a;
}

.tx-icon.payment {
    background: #fee2e2;
    color: #dc2626;
}

.tx-info {
    flex: 1;
}

.tx-title {
    display: block;
    font-weight: 500;
    color: #334155;
    font-size: 14px;
}

.tx-date {
    font-size: 12px;
    color: #94a3b8;
}

.tx-amount {
    font-weight: 600;
    font-size: 14px;
}

.tx-amount.positive {
    color: #16a34a;
}

.tx-amount.negative {
    color: #dc2626;
}

/* Methods List */
.btn-add {
    background: none;
    border: none;
    color: #3b82f6;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
}

.empty-method {
    background: #f8fafc;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    color: #64748b;
    border: 1px dashed #cbd5e1;
    font-size: 13px;
}

.wallets-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.wallet-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

.wallet-logo {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    object-fit: cover;
}

.wallet-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.w-name {
    font-weight: 600;
    font-size: 13px;
    color: #1e293b;
}

.w-status {
    font-size: 11px;
    color: #94a3b8;
}

.btn-link {
    padding: 6px 12px;
    background: #eff6ff;
    color: #3b82f6;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
}

.btn-link:hover {
    background: #dbeafe;
}

@media (max-width: 1024px) {
    .payment-stats {
        grid-template-columns: 1fr;
    }

    .content-grid {
        grid-template-columns: 1fr;
    }
}
</style>
