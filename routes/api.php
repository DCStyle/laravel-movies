<?php

use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\SeasonController;
use App\Http\Controllers\MovieController;
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

Route::post('/movies/import', [App\Http\Controllers\Api\MovieController::class, 'importMovie']);
Route::post('/movies/import-source', [App\Http\Controllers\Api\MovieController::class, 'importSource']);
Route::post('/movies/import-episode-source', [App\Http\Controllers\Api\MovieController::class, 'importEpisodeSource']);

Route::get('/movies/sources/{sourceId}', [MovieController::class, 'getSource']);
Route::get('/episodes/sources/{sourceId}', [MovieController::class, 'getSource'])
    ->defaults('type', 'episode');

Route::get('/seasons/{season}', [SeasonController::class, 'show']);

Route::get('/episodes/{episode}', [EpisodeController::class, 'show']);

Route::get('/movie-breaks/next', [App\Http\Controllers\MovieAdController::class, 'getNext']);