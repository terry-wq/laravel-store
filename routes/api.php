<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientAuthController;
use App\Http\Controllers\Api\CartController;




// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/productos', [ProductController::class, 'index']);

Route::get('/categorias', [CategoryController::class, 'index']);


Route::post('/register', [ClientAuthController::class, 'register']);
Route::post('/login', [ClientAuthController::class, 'login']);
Route::post('/logout', [ClientAuthController::class, 'logout']);

Route::middleware('api.token')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->client;
    });


});

Route::middleware('api.token')->group(function () {

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/remove', [CartController::class, 'remove']);
    Route::post('/cart/clear', [CartController::class, 'clear']);

});