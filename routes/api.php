<?php

use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    $user = $request->user();
    $avatar = $user->profile->profileImage();
    return response()->json([
        'user' => $user,
        'avatar' => $avatar
    ]);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile/{user}', [ProfileController::class, 'show']);
    Route::post('/profile/{user}/update', [ProfileController::class, 'update']);
    Route::get('/dashboard', [PostsController::class, 'show']);
    Route::get('/recommend', [FriendController::class, 'suggest']);
});
