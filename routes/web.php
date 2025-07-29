<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('sms-test', [HomeController::class, 'smsTest']);
//Route::get('lat-long', [HomeController::class, 'getLatLong']);
//Route::get('pdf', [HomeController::class, 'pdf']);
//Route::get('sundor', [HomeController::class, 'test']);
//Route::get('import-areas', [HomeController::class, 'importAreas']);

// Auth
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::get('user', [AuthController::class, 'user']);

