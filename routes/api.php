<?php

use App\Http\Controllers\OtpCodeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationCodeController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSEController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'cors'], function () {
//status
Route::get("/status", function () {
    return response()->json(["message" => "active"]);
});

//User model
Route::post("/register", [UserController::class, "register"]);
Route::post("/validateuser", [UserController::class, "login"]);
Route::post("/getuser", [UserController::class, "getuser"]);

//Store model
Route::get('/stores', [StoreController::class, 'index']);
Route::post('/stores', [StoreController::class, 'store']);
Route::get('/stores/{store}', [StoreController::class, 'show']);
Route::put('/stores/{store}', [StoreController::class, 'update']);
Route::delete('/stores/{store}', [StoreController::class, 'destroy']);

//Cart model
Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'store']);
Route::get('/cart/{userId}', [CartController::class, 'show']);
Route::put('/cart/{id}', [CartController::class, 'update']);
Route::delete('/cart/{id}', [CartController::class, 'destroy']);

//Otp model
Route::get('/getotp', [OtpCodeController::class, 'show']);
Route::post('/createotp', [OtpCodeController::class, 'store']);
Route::post('/deleteotp', [OtpCodeController::class, 'destroy']);
Route::post('/sentotp', [OtpCodeController::class, 'index']);

// Products model
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products48', [ProductsController::class, 'show48']);
Route::get('/products/searchname/{name}', [ProductsController::class, 'searchByName']);
Route::get('/products/searchstore/{storeId}', [ProductsController::class, 'showByStore']);
Route::get('/products/{product}', [ProductsController::class, 'show']);
Route::post('/products', [ProductsController::class, 'store']);
Route::put('/products/{id}', [ProductsController::class, 'update']);
Route::delete('/products/{product}', [ProductsController::class, 'destroy']);

//Categories model
Route::get('/categories', [CategoryController::class, 'index']);


});
