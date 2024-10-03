<?php

use App\Http\Controllers\ArticlesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\UserPreferencesController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user/preferences', [UserPreferencesController::class, 'get'])->middleware('auth:sanctum');
Route::post('/user/preferences', [UserPreferencesController::class, 'store'])->middleware('auth:sanctum');

Route::get('/newsfeed', [NewsfeedController::class, 'get'])->middleware('auth:sanctum');

Route::get('/articles/search', [ArticlesController::class, 'search']);
