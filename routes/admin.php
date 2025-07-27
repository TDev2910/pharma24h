<?php
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('categories', ProductCategoryController::class)->names('categories');
    Route::get('categories/{category}/edit', [ProductCategoryController::class, 'edit'])->name('categories.edit');
    // Route::put('categories/{category}', [ProductCategoryController::class, 'update'])->name('categories.update');
});

    