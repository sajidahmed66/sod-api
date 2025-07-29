<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\AddressController;
use App\Http\Controllers\Front\WishlistController;
use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\GeneralController;
use App\Http\Controllers\Front\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('mail-test', [GeneralController::class, 'mailTest']);

Route::prefix('vendor')->group(function () {
    include 'api/vendor.php';
});
Route::get('/health', function () {
    return response()->json(['status' => 'Okay', 'time' => now()]);
})->name('health');

// Auth
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('set-new-pass', [AuthController::class, 'setNewPassword']);

Route::get('settings', [GeneralController::class, 'settings']);
Route::get('top-notifications', [GeneralController::class, 'topNotifications']);
Route::get('sliders', [GeneralController::class, 'sliders']);
Route::post('contact-us', [GeneralController::class, 'contactUs']);
Route::get('social-links', [GeneralController::class, 'socialLinks']);
Route::get('static-pages/{id}', [GeneralController::class, 'staticPage']);

Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:customer')->group(function () {
    Route::get('user', [AuthController::class, 'user']);

    // Address
    Route::apiResource('addresses', AddressController::class);

    // Wishlist
    Route::apiResource('wishlists', WishlistController::class)->only('store', 'index');

    // Account
    Route::get('account/orders', [AccountController::class, 'getOrders']);
    Route::get('account/orders/{order}', [AccountController::class, 'getOrderDetails']);
    Route::post('account/change-password', [AccountController::class, 'changePassword']);
    Route::post('account/details', [AccountController::class, 'changeAccountDetails']);

    // Review
    Route::post('review', [ReviewController::class, 'addReview']);
    Route::post('review/eligible/{productId}', [ReviewController::class, 'checkEligibility']);
});

Route::get('categories', [CategoryController::class, 'index']);
Route::get('products/{slug}', [ProductController::class, 'details']);
Route::get('review/{productId}', [ReviewController::class, 'getReview']);
Route::get('new-products', [ProductController::class, 'newProducts']);
Route::get('related-products/{product:slug}', [ProductController::class, 'relatedProducts']);
Route::get('popular-products', [ProductController::class, 'popularProducts']);
Route::get('search-products', [ProductController::class, 'searchProducts']);
Route::get('hot-products', [ProductController::class, 'hotProducts']);
Route::get('category-products/{category}', [ProductController::class, 'categoryProducts']);

// Cart
Route::post('carts', [CartController::class, 'store']);
Route::get('carts', [CartController::class, 'index']);
Route::delete('carts/{cart}', [CartController::class, 'destroy']);

// Checkout
Route::post('checkout', [CheckoutController::class, 'store']);
Route::post('v2/checkout', [CheckoutController::class, 'storeV2']);
Route::get('order/{orderNo}', [CheckoutController::class, 'getOrderByOrderNo']);
Route::post('pay/{order}', [CheckoutController::class, 'pay']);
Route::get('cities', [CheckoutController::class, 'getCities']);
Route::get('areas', [CheckoutController::class, 'getAreas']);


