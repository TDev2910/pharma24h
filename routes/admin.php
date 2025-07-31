<?php
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('products/create-medicine', [ProductController::class, 'createMedicine'])->name('products.createMedicine');
    Route::post('products/store-medicine', [ProductController::class, 'storeMedicine'])->name('products.storeMedicine');
    
    // Route tạo thuốc mới
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}/detail', [ProductController::class, 'showDetail'])->name('products.detail');
    
    // Thêm route tạo mới đường dùng, hãng sản xuất, vị trí
    Route::post('products/drugroute', [ProductController::class, 'storeDrugRoute'])->name('products.drugroute.store');
    Route::post('products/manufacturer', [ProductController::class, 'storeManufacturer'])->name('products.manufacturer.store');
    Route::post('products/position', [ProductController::class, 'storePosition'])->name('products.position.store');

    // Route resource
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('categories', ProductCategoryController::class)->names('categories');
    Route::get('categories/{category}/edit', [ProductCategoryController::class, 'edit'])->name('categories.edit');
});
