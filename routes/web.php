<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DisplayProductController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/login',[\App\Http\Controllers\LoginController::class,'viewLoginAndRegister'])->name('login');
Route::post('/login',[\App\Http\Controllers\LoginController::class,'login'])->name('login');
Route::post('/register',[\App\Http\Controllers\LoginController::class,'register'])->name('register');
Route::post('/logout', [\App\Http\Controllers\LoginController::class,'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return view('AdminPage.Dashboard');
    })->name('adminDashboard');

    Route::resource('categories', \App\Http\Controllers\CategoriesController::class);

    Route::resource('products', \App\Http\Controllers\ProductController::class);

    Route::resource('customer', \App\Http\Controllers\CustomerAccountController::class);

    Route::resource('order-manage', \App\Http\Controllers\OrdersManageController::class);

//    Link Product Details

    Route::get('/product-details/{product}/details', [ProductDetailController::class, 'index'])->name('product-details.index');

    Route::post('/products/{product}/details', [ProductDetailController::class, 'store'])->name('product-details.store');

    Route::delete('/product-details/{details}', [ProductDetailController::class, 'destroy'])->name('product-details.destroy');

    Route::put('/product-details/{details}', [ProductDetailController::class, 'update'])->name('product-details.update');

//    Link Image Product

    Route::get('/product-images/{product}/images', [ProductImageController::class, 'index'])->name('product-images.index');

    Route::post('/products/{product}/images', [ProductImageController::class, 'upload'])->name('product-images.upload');

    Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('product-images.destroy');

    Route::put('/product-images/{image}', [ProductImageController::class, 'update'])->name('product-images.update');



});

Route::get('/', [\App\Http\Controllers\DisplayProductController::class, 'customerPage'])->name('customerPage');



Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/updatePassword', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // Hien thi san pham
    Route::get('/showProduct', [\App\Http\Controllers\DisplayProductController::class, 'index'])->name('showProduct');
    Route::get('/product-details/{product}', [DisplayProductController::class, 'productDetails'])->name('productDetails');

//    Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/cart/remove/{id}', [CartController::class, 'removeItem'])->name('cart.remove');

    Route::post('/get-colors-by-size', [ProductController::class, 'getColorsBySize']);
    Route::get('/products/category/{cateID}', [ProductController::class, 'filterByCategory'])->name('products.byCategory');

//    Checkout Control
    Route::get('/checkout', [OrdersController::class, 'index'])->name('checkout.index');
    Route::post('/orders', [OrdersController::class, 'storeFromCustomer'])->name('orders.storeFromCustomer');
    Route::get('/order-list', [OrdersController::class, 'showOrders'])->name('orders.showOrders');
    Route::get('/order-details{order}', [OrdersController::class, 'showDetails'])->name('orders.showDetails');

});

