<template>
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-value">{{ value }}</div>
      <div class="stat-label">{{ label }}</div>
      <component :is="trendComponent" v-if="trend" />
    </div>
    <div class="stat-icon-box" :class="iconColor">
      <i :class="icon"></i>
    </div>
  </div>
</template>

<script setup>
import { h } from 'vue'
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
    default: 'neutral', // positive, negative, neutral
    validator: (value) => ['positive', 'negative', 'neutral'].includes(value)
  },
  icon: {
    type: String,
    required: true
  },
  iconColor: {
    type: String,
    default: 'blue', // blue, purple, orange, red
    validator: (value) => ['blue', 'purple', 'orange', 'red'].includes(value)
  },
  linkTo: {
    type: String,
    default: ''
  }
})

const trendComponent = () => {
  if (props.linkTo) {
    return h(Link, {
      href: props.linkTo,
      class: 'stat-link'
    }, () => props.trend)
  } else {
    return h('div', {
      class: ['stat-trend', props.trendType]
    }, props.trend)
  }
}
</script>

<style scoped>
.stat-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #1E293B;
}

.stat-label {
  font-size: 14px;
  color: #64748B;
}

.stat-trend {
  font-size: 13px;
  font-weight: 500;
}

.stat-trend.positive {
  color: #10B981;
}

.stat-trend.negative {
  color: #EF4444;
}

.stat-trend.neutral {
  color: #6B7280;
}

.stat-link {
  font-size: 13px;
  color: #3B82F6;
  text-decoration: none;
  font-weight: 500;
  cursor: pointer;
}

.stat-link:hover {
  text-decoration: underline;
}

.stat-icon-box {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
}

.stat-icon-box.blue {
  background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
}

.stat-icon-box.purple {
  background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
}

.stat-icon-box.orange {
  background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
}

.stat-icon-box.red {
  background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
}
</style>

