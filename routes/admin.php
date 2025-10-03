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
use App\Http\Controllers\Admin\Supplier\PruchaseImportController;
use App\Http\Controllers\Admin\Supplier\PurchaseReturns;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () 
{
    // Dashboard / landing
    Route::get('products', [ProductController::class, 'index'])->name('products.index');

    // Medicines
    Route::prefix('medicines')->name('medicines.')->group(function () {
        Route::get('/', [MedicineController::class, 'index'])->name('index');
        Route::get('/list', [MedicineController::class, 'listMedicines'])->name('list');
        Route::post('/', [MedicineController::class, 'store'])->name('store');
        Route::get('/{medicine}/edit', [MedicineController::class, 'edit'])->name('edit');
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('update');
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('delete');
        Route::get('/{medicine}/detail', [MedicineController::class, 'show'])->name('detail');
    });

    // Goods
    Route::prefix('goods')->name('goods.')->group(function () {
        Route::get('/', [GoodsController::class, 'index'])->name('index');
        Route::get('/list', [GoodsController::class, 'listGoods'])->name('list');
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
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('delete');
        Route::get('/{service}/detail', [ServiceController::class, 'detail'])->name('detail');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
        Route::patch('/{service}/status', [ServiceController::class, 'updateStatus'])->name('updateStatus');
    });  

    // Legacy medicine routes (for backward compatibility)
    Route::get('products/create-medicine', [ProductController::class, 'createMedicine'])->name('products.createMedicine');
    Route::post('products/store-medicine', [ProductController::class, 'storeMedicine'])->name('products.storeMedicine');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}/detail', [ProductController::class, 'showDetail'])->name('products.detail');
    
    // Legacy goods routes (for backward compatibility)
    Route::get('products/create-goods', [ProductController::class,'createGoods'])->name('products.createGoods');
    Route::post('products/store-goods', [ProductController::class,'storeGoods'])->name('products.storeGoods');
    Route::get('products/goods', [ProductController::class,'listGoods'])->name('products.goods.list');
    Route::get('products/goods/{goods}/edit', [ProductController::class,'editGoods'])->name('products.goods.edit');
    Route::put('products/goods/{goods}', [ProductController::class,'updateGoods'])->name('products.goods.update');
    Route::delete('products/goods/{goods}', [ProductController::class,'deleteGoods'])->name('products.goods.delete');
    Route::get('products/goods/{goods}/detail', [ProductController::class,'showGoodsDetail'])->name('products.goods.detail');
    
    // Suppliers / Imports / Returns
    Route::resource('suppliers', SupplierController::class)->names('suppliers');
    Route::resource('import', PruchaseImportController::class)->names('import');
    Route::resource('purchase-returns', PurchaseReturns::class)->names('purchase-returns');
    Route::get('generate-import-code', [PruchaseImportController::class, 'generateImportCode'])->name('generate-import-code');
    Route::post('process-excel', [PruchaseImportController::class, 'processExcel'])->name('process-excel');

    // Supplier Categories
    Route::prefix('supplier-categories')->name('supplier-categories.')->group(function () {
        Route::get('/', [SupplierCategoryController::class, 'index'])->name('index');
        Route::post('/', [SupplierCategoryController::class, 'store'])->name('store');
        Route::put('/{supplierCategory}', [SupplierCategoryController::class, 'update'])->name('update');
        Route::delete('/{supplierCategory}', [SupplierCategoryController::class, 'destroy'])->name('destroy');
    });

    // Supporting entities
    Route::post('products/drugroute', [SupportingEntityController::class, 'storeDrugRoute'])->name('products.drugroute.store');
    Route::put('products/drugroute/{id}', [SupportingEntityController::class, 'updateDrugRoute'])->name('products.drugroute.update');
    Route::delete('products/drugroute/{id}', [SupportingEntityController::class, 'destroyDrugRoute'])->name('products.drugroute.destroy');
    
    Route::post('products/manufacturer', [SupportingEntityController::class, 'storeManufacturer'])->name('products.manufacturer.store');
    Route::put('products/manufacturer/{id}', [SupportingEntityController::class, 'updateManufacturer'])->name('products.manufacturer.update');
    Route::delete('products/manufacturer/{id}', [SupportingEntityController::class, 'destroyManufacturer'])->name('products.manufacturer.destroy');
    
    Route::post('products/position', [SupportingEntityController::class, 'storePosition'])->name('products.position.store');

    // Categories
    Route::resource('categories', ProductCategoryController::class)->except(['index', 'create', 'edit'])->names('categories');
    Route::get('categories/modal/data', [ProductCategoryController::class, 'getCategoriesForModal'])->name('categories.modal.data');
    Route::resource('products', ProductController::class)->except(['index'])->names('products');

    // Orders
    Route::resource('orders', OrdersController::class)->names('orders');
    Route::post('orders/{order}/update-status', [OrdersController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/invoice', [OrdersController::class, 'printInvoice'])->name('orders.invoice');
    Route::post('/orders/{id}/complete', [OrdersController::class, 'markCompleted'])->name('orders.complete');

    // Customers
    Route::resource('customers', CustomerController::class)->names('customers');
});