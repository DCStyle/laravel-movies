<?php

require __DIR__.'/auth.php';

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Management\AdController;
use App\Http\Controllers\Management\ArticleController;
use App\Http\Controllers\Management\CategoryController;
use App\Http\Controllers\Management\EpisodeController;
use App\Http\Controllers\Management\FooterController;
use App\Http\Controllers\Management\MenuController;
use App\Http\Controllers\Management\MovieAdController;
use App\Http\Controllers\Management\MovieManagementController;
use App\Http\Controllers\Management\PageController;
use App\Http\Controllers\Management\SeasonController;
use App\Http\Controllers\Management\SettingsController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Movies Management Routes
Route::middleware(['auth', 'role:admin|mod'])->prefix('management')->name('management.')->group(function () {
    Route::prefix('movies')->name('movies.')->controller(MovieManagementController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{movie}/edit', 'edit')->name('edit');
        Route::put('/{movie}', 'update')->name('update');
        Route::delete('/{movie}', 'destroy')->name('destroy');
    });

    Route::prefix('movies/{movie}/seasons')->name('seasons.')->group(function () {
        Route::get('/', [SeasonController::class, 'index'])->name('index');
        Route::post('/', [SeasonController::class, 'store'])->name('store');
        Route::put('/{season}', [SeasonController::class, 'update'])->name('update');
        Route::delete('/{season}', [SeasonController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('seasons/{season}/episodes')->name('episodes.')->group(function () {
        Route::get('/', [EpisodeController::class, 'index'])->name('index');
        Route::get('/create', [EpisodeController::class, 'create'])->name('create');
        Route::post('/', [EpisodeController::class, 'store'])->name('store');
        Route::get('/{episode}/edit', [EpisodeController::class, 'edit'])->name('edit');
        Route::put('/{episode}', [EpisodeController::class, 'update'])->name('update');
        Route::delete('/{episode}', [EpisodeController::class, 'destroy'])->name('destroy');
    });

    // Categories Management
    Route::resource('categories', CategoryController::class);

    // Article Management
    Route::resource('articles', ArticleController::class);

    // Settings
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/settings', 'index')->name('settings');
        Route::put('/settings', 'update')->name('settings.update');
    });

    // Ads Management
    Route::resource('ads', AdController::class)->except(['show']);
    Route::post('ads/reorder', [AdController::class, 'reorder'])->name('ads.reorder');

    // Pages Management
    Route::resource('pages', PageController::class)->except(['show']);

    // Menu Management
    Route::resource('menus', MenuController::class)->except(['show']);
    Route::post('menus/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');

    // Movie Ads Management
    Route::resource('movie-ads', MovieAdController::class);
    Route::post('movie-ads/{movieAd}/toggle-status', [MovieAdController::class, 'toggleStatus'])
        ->name('movie-ads.toggle-status');
    Route::post('movie-ads/update-order', [MovieAdController::class, 'updateOrder'])
        ->name('movie-ads.update-order');

    // Footer Management
    Route::prefix('footer')->controller(FooterController::class)->name('footer.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/column', 'storeColumn')->name('column.store');
        Route::post('/column/{column}/item', 'storeColumnItem')->name('column.item.store');
        Route::post('/setting', 'updateSetting')->name('setting.update');

        // AJAX routes
        Route::post('/column/update-order', 'updateColumnOrder')->name('column.updateOrder');
        Route::post('/item/update-order', 'updateItemOrder')->name('item.updateOrder');
        Route::post('/item/update-parent', 'updateItemParent')->name('item.updateParent');
        Route::post('/update', 'updateFooter')->name('update');
        Route::delete('/delete', 'deleteFooter')->name('delete');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Redirect to Dashboard
    Route::redirect('/', '/admin/dashboard');

    // Genres Management
    Route::resource('genres', GenreController::class);

    // Users Management
    Route::get('users/activity', [UserController::class, 'activity'])->name('users.activity');
    Route::resource('users', UserController::class);
});

// Moderator Routes
Route::middleware(['auth', 'role:mod'])->prefix('mod')->name('mod.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Mod\DashboardController::class, 'index'])->name('dashboard');

    // Redirect to Dashboard
    Route::redirect('/', '/mod/dashboard');
});

// Public Routes
Route::get('/', [MovieController::class, 'index'])->name('home');
Route::get('/{slug}', [App\Http\Controllers\IndexController::class, 'show'])->name('index.show');
Route::prefix('phim')->group(function () {
    Route::get('/{movie:slug}', [MovieController::class, 'show'])->name('movies.show');
    Route::get('/{movie:slug}/season/{season}/episode/{episode}', [MovieController::class, 'episode'])
        ->name('movies.episode')
        ->where(['season' => '[0-9]+', 'episode' => '[0-9]+']);
});
Route::get('/tim-kiem', [MovieController::class, 'search'])->name('movies.search');
Route::get('/the-loai/{genre:slug}', [App\Http\Controllers\GenreController::class, 'show'])->name('genres.show');
Route::get('/quoc-gia/{country:slug}', [CountryController::class, 'show'])->name('countries.show');
Route::get('/nam-phat-hanh/{year}', [App\Http\Controllers\ReleaseYearController::class, 'show'])->name('release-years.show');
Route::get('/tin-tuc', [App\Http\Controllers\ArticleController::class, 'index'])->name('articles.index');
Route::get('/tin-tuc/{article:slug}', [App\Http\Controllers\ArticleController::class, 'show'])->name('articles.show');

// API Routes
Route::post('images/upload', [ImageController::class, 'store'])->name('images.upload');