# Quick Reference - Components Structure

## Component Import Paths

### Global Components
```javascript
// Header & Footer
import Header from '@/Components/Global/Header.vue'
import Footer from '@/Components/Global/Footer.vue'

// Chat
import ChatbotFloatingButton from '@/Components/Global/Chat/ChatbotFloatingButton.vue'
import ChatbotPopup from '@/Components/Global/Chat/ChatbotPopup.vue'
import VchatWidget from '@/Components/Global/Chat/VchatWidget.vue'
```

### Home Components
```javascript
import BannerSection from '@/Components/Home/BannerSection.vue'
import IntroSection from '@/Components/Home/IntroSection.vue'
import FeatureIcons from '@/Components/Home/FeatureIcons.vue'
import ServiceSection from '@/Components/Home/ServiceSection.vue'
```

### Product Components
```javascript
import ProductCard from '@/Components/Product/ProductCard.vue'
```

### User Components
```javascript
// Sidebar
import Sidebar from '@/Components/User/Sidebar/Sidebar.vue'

// Dashboard
import StatsCard from '@/Components/User/Dashboard/StatsCard.vue'
import RecentOrders from '@/Components/User/Dashboard/RecentOrders.vue'

// Order
import OrderTable from '@/Components/User/Order/OrderTable.vue'
import OrderStatus from '@/Components/User/Order/OrderStatus.vue'

// Profile
import InforForm from '@/Components/User/Profile/InforForm.vue'
import PasswordForm from '@/Components/User/Profile/PasswordForm.vue'
```

---

## Usage Examples

### ProductCard
```vue
<ProductCard 
  :product="productData" 
  type="medicine"
/>
<!-- or -->
<ProductCard 
  :product="productData" 
  type="goods"
/>
```

### StatsCard
```vue
<StatsCard 
  :value="150"
  label="Đơn hàng"
  trend="+10 hôm nay"
  trend-type="positive"
  icon="fas fa-shopping-cart"
  icon-color="blue"
/>
```

### OrderTable
```vue
<OrderTable 
  :orders="orders" 
  @order-click="handleOrderClick"
/>
```

### OrderStatus
```vue
<OrderStatus status="pending" />
<OrderStatus status="completed" />
<OrderStatus status="cancelled" />
```

### InforForm & PasswordForm
```vue
<InforForm 
  :auth="auth" 
  :errors="errors"
  :flash="flash"
  :loading="loading"
  @submit="handleSubmit"
/>

<PasswordForm 
  :errors="errors"
  :flash="flash"
  :loading="loading"
  @submit="handlePasswordChange"
/>
```

---

## File Size Comparison

| Page | Before | After | Reduction |
|------|--------|-------|-----------|
| Home.vue | 481 | 73 | 85% |
| UserLayout.vue | 237 | 100 | 58% |
| Dashboard.vue | 616 | 396 | 36% |
| Orders/Index.vue | 571 | 178 | 69% |
| ProfileSettings.vue | 852 | 248 | 71% |

---

## Component Props Quick Reference

### ProductCard
- `product` (Object, required)
- `type` (String, required): 'medicine' | 'goods'

### Sidebar
- `auth` (Object)
- `unreadNotificationsCount` (Number, default: 0)

### StatsCard
- `value` (String|Number, required)
- `label` (String, required)
- `trend` (String)
- `trendType` (String): 'positive' | 'negative' | 'neutral'
- `icon` (String, required)
- `iconColor` (String): 'blue' | 'purple' | 'orange' | 'red'
- `linkTo` (String)

### OrderTable
- `orders` (Array, default: [])
- **Emit:** `order-click(orderId)`

### OrderStatus
- `status` (String, required)

### InforForm
- `auth` (Object, required)
- `errors` (Object, default: {})
- `flash` (Object, default: {})
- `loading` (Boolean, default: false)
- **Emit:** `submit(formData)`

### PasswordForm
- `errors` (Object, default: {})
- `flash` (Object, default: {})
- `loading` (Boolean, default: false)
- **Emit:** `submit(formData)`

---

## Folder Structure Tree

```
resources/js/Components/
├── Global/
│   ├── Header.vue
│   ├── Footer.vue
│   └── Chat/
│       ├── ChatbotFloatingButton.vue
│       ├── ChatbotPopup.vue
│       └── VchatWidget.vue
├── Home/
│   ├── BannerSection.vue
│   ├── IntroSection.vue
│   ├── FeatureIcons.vue
│   └── ServiceSection.vue
├── Product/
│   └── ProductCard.vue
└── User/
    ├── Sidebar/
    │   └── Sidebar.vue
    ├── Dashboard/
    │   ├── StatsCard.vue
    │   └── RecentOrders.vue
    ├── Order/
    │   ├── OrderTable.vue
    │   └── OrderStatus.vue
    └── Profile/
        ├── InforForm.vue
        └── PasswordForm.vue
```

