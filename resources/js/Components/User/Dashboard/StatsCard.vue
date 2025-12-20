<template>
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-value">{{ value }}</div>
      <div class="stat-label">{{ label }}</div>
      <Link v-if="linkTo" :href="linkTo" class="stat-link">{{ trend }}</Link>
      <div v-else-if="trend" :class="['stat-trend', trendType]">{{ trend }}</div>
    </div>
    <div class="stat-icon-box" :class="iconColor">
      <i :class="icon"></i>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  value: {
    type: [String, Number],
    required: true
  },
  label: {
    type: String,
    required: true
  },
  trend: {
    type: String,
    default: ''
  },
  trendType: {
    type: String,
    default: 'neutral',
    validator: (value) => ['positive', 'negative', 'neutral'].includes(value)
  },
  icon: {
    type: String,
    required: true
  },
  iconColor: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'purple', 'orange', 'red'].includes(value)
  },
  linkTo: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  border: 1px solid #F1F5F9;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1E293B;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 13px;
  color: #64748B;
  margin-bottom: 8px;
}

.stat-trend {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 4px;
  display: inline-block;
}

.stat-trend.positive {
  background: #DCFCE7;
  color: #16A34A;
}

.stat-trend.neutral {
  background: #F1F5F9;
  color: #64748B;
}

.stat-link {
  font-size: 12px;
  color: #3B82F6;
  text-decoration: none;
}

.stat-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.stat-icon-box.blue {
  background: #EFF6FF;
  color: #3B82F6;
}

.stat-icon-box.purple {
  background: #F3E8FF;
  color: #9333EA;
}

.stat-icon-box.orange {
  background: #FFEDD5;
  color: #F97316;
}

.stat-icon-box.red {
  background: #FEE2E2;
  color: #EF4444;
}
</style>
