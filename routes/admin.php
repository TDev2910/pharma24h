<?php

use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Product\MedicineController;
use App\Http\Controllers\Admin\Product\GoodsController;
use App\Http\Controllers\Admin\Product\ServiceController;
use App\Http\Controllers\Admin\Product\SupportingEntityController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\Supplier\SupplierController;
use App\Http\Controllers\Admin\Supplier\SupplierCategoryController;
use App\Http\Controllers\Admin\Order\OrdersController;
use App\Http\Controllers\Admin\Customer\CustomerController;
use App\Http\Controllers\Admin\Doctor\DoctorController;
use App\Http\Controllers\Admin\Supplier\PruchaseImportController;
use App\Http\Controllers\Admin\Supplier\PurchaseReturnsController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\OrderServices\ServiceBookingController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Employee\ScheduleController;
use App\Http\Controllers\Admin\Employee\ShiftController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('admin')->name('admin.')->group(function () {
    // DASHBOARD & OVERVIEW ROUTES

    // Dashboard - Vue Component
    Route::get('products', function () {
        return Inertia::render('Admin/Products/Overviews/Dashboard');
    })->name('products.index');

    Route::get('/services-dashboard', function () {
        return Inertia::render('Admin/Orders/Services/Dashboard');
    })->name('admin.services.dashboard');

    // Supporting entities
    // Drug Route
    Route::get('products/drugroute', [SupportingEntityController::class, 'indexDrugRoute'])->name('products.drugroute.index');
    Route::post('products/drugroute', [SupportingEntityController::class, 'storeDrugRoute'])->name('products.drugroute.store');
    Route::put('products/drugroute/{id}', [SupportingEntityController::class, 'updateDrugRoute'])->name('products.drugroute.update');
    Route::delete('products/drugroute/{id}', [SupportingEntityController::class, 'destroyDrugRoute'])->name('products.drugroute.destroy');

    // Manufacturer
    Route::get('products/manufacturer', [SupportingEntityController::class, 'indexManufacturer'])->name('products.manufacturer.index');
    Route::post('products/manufacturer', [SupportingEntityController::class, 'storeManufacturer'])->name('products.manufacturer.store');
    Route::put('products/manufacturer/{id}', [SupportingEntityController::class, 'updateManufacturer'])->name('products.manufacturer.update');
    Route::delete('products/manufacturer/{id}', [SupportingEntityController::class, 'destroyManufacturer'])->name('products.manufacturer.destroy');

    // Position
    Route::get('products/position', [SupportingEntityController::class, 'indexPosition'])->name('products.position.index');
    Route::post('products/position', [SupportingEntityController::class, 'storePosition'])->name('products.position.store');
    Route::put('products/position/{id}', [SupportingEntityController::class, 'updatePosition'])->name('products.position.update');
    Route::delete('products/position/{id}', [SupportingEntityController::class, 'destroyPosition'])->name('products.position.destroy');

    // PRODUCT MANAGEMENT ROUTES

    // Medicines
    Route::prefix('medicines')->name('medicines.')->group(function () {
        Route::get('/', [MedicineController::class, 'index'])->name('index');
        Route::get('/api', [MedicineController::class, 'apiIndex'])->name('api');
        Route::get('/list', [MedicineController::class, 'listMedicines'])->name('list');
        Route::get('/generate-codes', [MedicineController::class, 'generateCodes'])->name('generate-codes');
        Route::post('/', [MedicineController::class, 'store'])->name('store');
        Route::get('/{medicine}/edit', [MedicineController::class, 'edit'])->name('edit');
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('update');
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('delete');
        Route::get('/{medicine}/detail', [MedicineController::class, 'show'])->name('detail');
    });

    // Goods
    Route::prefix('goods')->name('goods.')->group(function () {
        Route::get('/', [GoodsController::class, 'index'])->name('index');
        Route::get('/api', [GoodsController::class, 'apiIndex'])->name('api');
        Route::get('/list', [GoodsController::class, 'vueListGoods'])->name('list');
        Route::get('/vue-list', [GoodsController::class, 'vueListGoods'])->name('vue-list');
        Route::get('/generate-codes', [GoodsController::class, 'generateCodes'])->name('generate-codes');
        Route::get('/inventory', [GoodsController::class, 'inventory'])->name('inventory');
        Route::post('/', [GoodsController::class, 'store'])->name('store');
        Route::get('/{goods}/edit', [GoodsController::class, 'edit'])->name('edit');
        Route::put('/{goods}', [GoodsController::class, 'update'])->name('update');
        Route::delete('/{goods}', [GoodsController::class, 'destroy'])->name('delete');
        Route::get('/{goods}/detail', [GoodsController::class, 'show'])->name('detail');
    });

    // Services
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/list', [ServiceController::class, 'listServices'])->name('list');
        Route::get('products/services/list', [ServiceController::class, 'listServices'])->name('products.services.list');
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('delete');
        Route::get('/{service}/detail', [ServiceController::class, 'detail'])->name('detail');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
        Route::patch('/{service}/status', [ServiceController::class, 'updateStatus'])->name('updateStatus');
    });

    // Categories
    Route::resource('categories', ProductCategoryController::class)->except(['index', 'create', 'edit'])->names('categories');
    Route::get('categories/modal/data', [ProductCategoryController::class, 'getCategoriesForModal'])->name('categories.modal.data');
    Route::post('categories', [ProductCategoryController::class, 'store'])->name('categories.store');
    Route::resource('products', ProductController::class)->except(['index'])->names('products');
    // ORDER MANAGEMENT ROUTES

    // Orders
    Route::resource('orders', OrdersController::class)->names('orders');
    Route::post('orders/{order}/update-status', [OrdersController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/invoice', [OrdersController::class, 'printInvoice'])->name('orders.invoice');
    Route::post('/orders/{id}/complete', [OrdersController::class, 'markCompleted'])->name('orders.complete');

    // Service Bookings
    Route::prefix('service-bookings')->name('service-bookings.')->group(function () {
        Route::get('/', [ServiceBookingController::class, 'index'])->name('index');
        Route::get('/{id}', [ServiceBookingController::class, 'show'])->name('show');
        Route::post('/{id}/confirm', [ServiceBookingController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/cancel', [ServiceBookingController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/mark-paid', [ServiceBookingController::class, 'markAsPaid'])->name('mark-paid');
        Route::post('/{id}/complete', [ServiceBookingController::class, 'complete'])->name('complete');
    });
    // CUSTOMER & USER MANAGEMENT ROUTES

    // Customers
    Route::resource('customers', CustomerController::class)->names('customers');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Employees
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/api', [EmployeeController::class, 'apiIndex'])->name('api');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('/resources/data', [EmployeeController::class, 'getResources'])->name('resources');
        Route::get('/generate/code', [EmployeeController::class, 'generateCode'])->name('generate-code');
    });

    // Employee Schedules
    Route::prefix('employee-schedules')->name('employee-schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/api', [ScheduleController::class, 'getSchedules'])->name('api');
        Route::get('/api/weekly', [ScheduleController::class, 'getWeeklySchedules'])->name('api.weekly');
        Route::post('/', [ScheduleController::class, 'store'])->name('store');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

    // Shifts
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('/', [ShiftController::class, 'index'])->name('index');
        Route::get('/api', [ShiftController::class, 'apiIndex'])->name('api');
        Route::post('/', [ShiftController::class, 'store'])->name('store');
        Route::put('/{shift}', [ShiftController::class, 'update'])->name('update');
        Route::delete('/{shift}', [ShiftController::class, 'destroy'])->name('destroy');
    });
    // Doctors
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/api', [DoctorController::class, 'getDoctors'])->name('doctors.api');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    Route::get('/doctors/generate-code', [DoctorController::class, 'generateDoctorCode'])->name('doctors.generate-code');
    Route::post('/doctors/upload-avatar', [DoctorController::class, 'uploadAvatar'])->name('doctors.upload-avatar');
    // SUPPLIER & PURCHASE MANAGEMENT ROUTES

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->names('suppliers');
    Route::get('suppliers/{supplier}/imports', [SupplierController::class, 'getImports'])->name('suppliers.imports');
    Route::get('suppliers/{supplier}/returns', [SupplierController::class, 'getReturns'])->name('suppliers.returns');
    Route::get('imports/{importCode}/items', [SupplierController::class, 'getImportItems'])->name('imports.items'); // ← Thêm dòng này
    Route::get('returns/{returnCode}/items', [SupplierController::class, 'getReturnItems'])->name('returns.items'); // ← Thêm dòng này
    // Supplier Categories
    Route::prefix('supplier-categories')->name('supplier-categories.')->group(function () {
        Route::get('/', [SupplierCategoryController::class, 'index'])->name('index');
        Route::post('/', [SupplierCategoryController::class, 'store'])->name('store');
        Route::put('/{supplierCategory}', [SupplierCategoryController::class, 'update'])->name('update');
        Route::delete('/{supplierCategory}', [SupplierCategoryController::class, 'destroy'])->name('destroy');
    });

    // Purchase Orders
    // Đặt route export và các route cụ thể TRƯỚC route resource để tránh xung đột
    Route::get('purchase-orders/api', [PruchaseImportController::class, 'apiIndex'])->name('purchase-orders.api')->middleware('auth');
    Route::get('purchase-orders/export', [PruchaseImportController::class, 'export'])->name('purchase-orders.export')->middleware('auth');
    Route::get('purchase-orders/{id}/export', [PruchaseImportController::class, 'exportSingle'])
        ->name('purchase-orders.export-single')
        ->middleware('auth');
    Route::resource('purchase-orders', PruchaseImportController::class)->names('purchase-orders');
    Route::get('generate-import-code', [PruchaseImportController::class, 'generateImportCode'])->name('generate-import-code');
    Route::post('process-excel', [PruchaseImportController::class, 'processExcel'])->name('process-excel');

    // Purchase Returns
    // Đặt route export và các route cụ thể trước route resource để tránh xung đột
    Route::get('purchase-returns/export', [PurchaseReturnsController::class, 'export'])->name('purchase-returns.export')->middleware('auth');
    Route::get('purchase-returns/{id}/export', [PurchaseReturnsController::class, 'exportSingle'])
        ->name('purchase-returns.export-single')
        ->middleware('auth');
    Route::post('purchase-returns/process-excel', [PurchaseReturnsController::class, 'processExcel'])->name('purchase-returns.process-excel');
    Route::get('purchase-returns/create-sample', [PurchaseReturnsController::class, 'createSampleData'])->name('purchase-returns.create-sample');
    Route::resource('purchase-returns', PurchaseReturnsController::class)->names('purchase-returns');
    Route::get('generate-return-code', [PurchaseReturnsController::class, 'generateReturnCode'])->name('generate-return-code');

    // Import/Export
    Route::resource('import', PruchaseImportController::class)->names('import');
    Route::prefix('import')->name('import.')->group(function () {
        Route::post('stock-import', [ImportController::class, 'processStockImportExcel'])->name('stock-import');
        Route::post('orders', [ImportController::class, 'processOrderExcel'])->name('orders');
        Route::post('products', [ImportController::class, 'processProductExcel'])->name('products');
    });

    // ========================================
    // LEGACY ROUTES (for backward compatibility)
    // ========================================

    // Legacy medicine routes
    Route::get('products/create-medicine', [ProductController::class, 'createMedicine'])->name('products.createMedicine');
    Route::post('products/store-medicine', [ProductController::class, 'storeMedicine'])->name('products.storeMedicine');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}/detail', [ProductController::class, 'showDetail'])->name('products.detail');

    // Legacy goods routes
    Route::get('products/create-goods', [ProductController::class, 'createGoods'])->name('products.createGoods');
    Route::post('products/store-goods', [ProductController::class, 'storeGoods'])->name('products.storeGoods');
    Route::get('products/goods', [ProductController::class, 'listGoods'])->name('products.goods.list');
    Route::get('products/goods/{goods}/edit', [ProductController::class, 'editGoods'])->name('products.goods.edit');
    Route::put('products/goods/{goods}', [ProductController::class, 'updateGoods'])->name('products.goods.update');
    Route::delete('products/goods/{goods}', [ProductController::class, 'deleteGoods'])->name('products.goods.delete');
    Route::get('products/goods/{goods}/detail', [ProductController::class, 'showGoodsDetail'])->name('products.goods.detail');
});
