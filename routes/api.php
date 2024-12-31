<?php

use App\Http\Controllers\Admin\MovieAdController;
use App\Http\Controllers\MovieController;
use App\Models\MovieAd;
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
    return $request->user();
});

Route::get('/search', [MovieController::class, 'search']);
Route::get('/movies/sources/{sourceId}', [MovieController::class, 'getSource']);
Route::get('/movie-ads/next', [App\Http\Controllers\MovieAdController::class, 'getNextAd']);