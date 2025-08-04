# System Patterns - Suckhoe24h

## Architecture Overview
Hệ thống được xây dựng theo mô hình MVC (Model-View-Controller) của Laravel với các layer rõ ràng:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Views       │    │   Controllers   │    │     Models      │
│  (Blade Files)  │◄──►│  (Admin/User)   │◄──►│  (Eloquent)     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Middleware    │    │   Database      │
│ (Bootstrap/JS)  │    │  (Auth/Admin)   │    │   (MySQL)       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Key Design Patterns

### 1. Repository Pattern (Eloquent ORM)
```php
// Models with relationships
class Medicine extends Model {
    public function manufacturer() {
        return $this->belongsTo(Manufacturer::class);
    }
    
    public function drugRoute() {
        return $this->belongsTo(DrugRoute::class, 'drugusage_id');
    }
}
```

### 2. Modal Pattern for CRUD Operations
- **Create Modal**: `createmedicine.blade.php`
- **Edit Modal**: `editmedicine.blade.php`
- **Shared Components**: `unit_modal.blade.php`

### 3. AJAX Pattern for Dynamic Updates
```javascript
// Fetch data for edit modal
fetch(`/admin/medicines/${medicineId}/detail`)
    .then(response => response.json())
    .then(data => {
        // Populate form fields
    });
```

### 4. Form Submission Pattern
```javascript
// DELETE request via form submission
const form = document.createElement('form');
form.method = 'POST';
form.action = `/admin/medicines/${medicineId}`;
form.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
```

## Component Relationships

### Medicine Management Flow
```
User Action → Controller → Model → Database
     ↓           ↓         ↓         ↓
Modal Form → Validation → Eloquent → MySQL
     ↓           ↓         ↓         ↓
JavaScript → Response → Relationship → Query
```

### File Structure Patterns
```
resources/views/admin/products/Danhsachhanghoa/
├── index.blade.php              # Main listing page
├── createmedicine.blade.php     # Create modal
├── editmedicine.blade.php       # Edit modal
└── formmodal/
    └── unit_modal.blade.php     # Shared component
```

## Database Schema Patterns

### Foreign Key Convention
- **Correct**: `manufacturer_id`, `drugusage_id`, `position_id`
- **Incorrect**: `manufacturer` (causes relationship issues)

### Migration Patterns
```php
// Rename column migration
Schema::table('medicines', function (Blueprint $table) {
    $table->renameColumn('manufacturer', 'manufacturer_id');
});
```

## Frontend Patterns

### Bootstrap 5 Integration
- **Utility Classes**: `d-flex`, `align-items-center`, `justify-content-between`
- **Component Classes**: `btn`, `modal`, `table`, `toast`
- **Responsive**: Mobile-first approach

### JavaScript Patterns
```javascript
// Modal management
const modal = new bootstrap.Modal(document.getElementById('modalId'));
modal.show();

// Form population
Object.keys(fields).forEach(fieldId => {
    const element = document.getElementById(fieldId);
    if (element) element.value = fields[fieldId];
});
```

## Security Patterns

### CSRF Protection
```php
// Laravel automatically includes CSRF token
@csrf
<input type="hidden" name="_method" value="PUT">
```

### Authorization Middleware
```php
// Admin routes protection
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes here
});
```

## Error Handling Patterns

### Controller Error Handling
```php
try {
    $medicine = Medicine::with(['category', 'manufacturer'])->findOrFail($id);
    return response()->json(['success' => true, 'product' => $medicine]);
} catch (\Exception $e) {
    return response()->json(['success' => false, 'message' => 'Error']);
}
```

### Frontend Error Handling
```javascript
.catch(error => {
    alert('Đã xảy ra lỗi khi tải thông tin thuốc!');
});
``` 