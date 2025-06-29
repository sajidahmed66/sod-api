<?php

use App\Http\Controllers\Vendor\AccountingController;
use App\Http\Controllers\Vendor\InventoryLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\AuthController;
use App\Http\Controllers\Vendor\CategoryController;
use App\Http\Controllers\Vendor\SubCategoryController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\SettingController;
use App\Http\Controllers\Vendor\TopNotificationController;
use App\Http\Controllers\Vendor\StaticPageController;
use App\Http\Controllers\Vendor\SocialLinkController;
use App\Http\Controllers\Vendor\SliderController;
use App\Http\Controllers\Vendor\CustomerController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Vendor\TransactionController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\SmsController;
use App\Http\Controllers\Vendor\ResetPasswordController;

// Auth
Route::post('login', [AuthController::class, 'login']);
Route::get('vendor-details', [AuthController::class, 'vendorDetails']);
Route::get('/orders-download', [ProductController::class, 'downloadExcel']);
// Reset Password
Route::post('forgot-password', [ResetPasswordController::class, 'resetPasswordEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);
Route::get('reset-password/{vendor}/{token}/{email}', [ResetPasswordController::class, 'checkToken'])
    ->name('vendor.password.reset');

Route::middleware('auth:vendor')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Dashboard
    Route::get('dashboard/top-widgets', [DashboardController::class, 'topWidgets']);
    Route::get('dashboard/sales-chart', [DashboardController::class, 'salesChart']);
    Route::post('dashboard-data', [DashboardController::class, 'getAllByDateRange']);

    // Category
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('categories.sub-categories', SubCategoryController::class);

    // Product
    Route::apiResource('products', ProductController::class);
    Route::get('/product/{product_id}/prices', [ProductController::class, 'getPrices']);
    Route::get('/orders-download', [ProductController::class, 'downloadExcel']);

    // Order
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/status', [OrderController::class, 'changeStatus']);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'getInvoice']);
    Route::get('cities', [CheckoutController::class, 'getCities']);
    Route::get('areas', [CheckoutController::class, 'getAreas']);

    // Settings
    Route::get('settings', [SettingController::class, 'show']);
    Route::post('settings/payment-methods', [SettingController::class, 'paymentMethodsStore']);
    Route::post('settings/shipping-cost', [SettingController::class, 'shippingCostStore']);
    Route::post('settings', [SettingController::class, 'store']);
    Route::get('settings/notifications', [SettingController::class, 'getNotificationsSettings']);
    Route::post('settings/notifications', [SettingController::class, 'setNotificationsSettings']);
    Route::get('couriers', [SettingController::class, 'getCouriers']);
    Route::apiResource('top-notifications', TopNotificationController::class);
    Route::post('static-pages', [StaticPageController::class, 'store']);
    Route::get('static-pages/{id}', [StaticPageController::class, 'show']);
    Route::apiResource('social-links', SocialLinkController::class);
    Route::apiResource('sliders', SliderController::class);

    // Customer
    Route::apiResource('customers', CustomerController::class);
    Route::get('customer-info/{mobile}', [CustomerController::class, 'getCustomerData']);

    // Transaction
    Route::apiResource('transactions', TransactionController::class);

    // SMS
    Route::get('/sms-logs', [SmsController::class, 'smsLogs']);

    Route::post('/inventory-logs', [InventoryLogController::class, 'saveLog']);
    Route::get('/inventory-logs', [InventoryLogController::class, 'getAllInventoryLogs']);
    Route::get('/inventory-logs/{id}', [InventoryLogController::class, 'getAllInventoryLog']);
    Route::post('/inventory-logs/{id}', [InventoryLogController::class, 'updateInventoryLog']);
//    Route::delete('/inventory-logs/{id}', [InventoryLogController::class, 'deleteInventoryLog']);
    Route::get('/inventory-log-delete/{id}', [InventoryLogController::class, 'deleteInventoryLog']);

    Route::get('/accounting-data', [AccountingController::class, 'getAccountingData']);
    Route::post('/accounting-data', [AccountingController::class, 'save']);
    Route::get('/accounting-data/{id}', [AccountingController::class, 'getSingleAccounting']);
    Route::post('/accounting-data/{id}', [AccountingController::class, 'updateAccounting']);
    Route::get('/accounting-data-delete/{id}', [AccountingController::class, 'deleteAccounting']);
});
