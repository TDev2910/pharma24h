<?php
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () 
{
    Route::get('products/create-medicine', [ProductController::class, 'createMedicine'])->name('products.createMedicine');
    Route::post('products/store-medicine', [ProductController::class, 'storeMedicine'])->name('products.storeMedicine');
    Route::get('medicines', [ProductController::class, 'listMedicines'])->name('medicines.list');
    Route::post('medicines', [ProductController::class, 'storeMedicine'])->name('medicines.store');
    Route::get('medicines/{medicine}/edit', [ProductController::class, 'editMedicine'])->name('medicines.edit');
    Route::put('medicines/{medicine}', [ProductController::class, 'updateMedicine'])->name('medicines.update');
    Route::delete('medicines/{medicine}', [ProductController::class, 'deleteMedicine'])->name('medicines.delete');
    Route::get('medicines/{medicine}/detail', [ProductController::class, 'showDetail'])->name('medicines.detail');
    // Route tạo thuốc mới
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}/detail', [ProductController::class, 'showDetail'])->name('products.detail');
    //Route tao hang hoa
    Route::get('products/create-goods', [ProductController::class,'createGoods'])->name('products.createGoods');
    Route::post('products/store-goods', [ProductController::class,'storeGoods'])->name('products.storeGoods');
    Route::get('products/goods', [ProductController::class,'listGoods'])->name('products.goods.list');
    Route::get('products/goods/{goods}/edit', [ProductController::class,'editGoods'])->name('products.goods.edit');
    Route::put('products/goods/{goods}', [ProductController::class,'updateGoods'])->name('products.goods.update');
    Route::delete('products/goods/{goods}', [ProductController::class,'deleteGoods'])->name('products.goods.delete');
    Route::get('products/goods/{goods}/detail', [ProductController::class,'showGoodsDetail'])->name('products.goods.detail');
    // Thêm route cho goods detail API
    Route::get('goods/{goods}/detail', [ProductController::class,'showGoodsDetail'])->name('goods.detail');
    // Thêm route cho goods store
    Route::post('goods', [ProductController::class, 'storeGoods'])->name('goods.store');
    // Thêm route tạo mới đường dùng, hãng sản xuất, vị trí
    Route::post('products/drugroute', [ProductController::class, 'storeDrugRoute'])->name('products.drugroute.store');
    Route::post('products/manufacturer', [ProductController::class, 'storeManufacturer'])->name('products.manufacturer.store');
    Route::post('products/position', [ProductController::class, 'storePosition'])->name('products.position.store');

    // Route resource
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('categories', ProductCategoryController::class)->names('categories');
    Route::get('categories/{category}/edit', [ProductCategoryController::class, 'edit'])->name('categories.edit');
});