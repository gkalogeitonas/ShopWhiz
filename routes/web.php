<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

        // Tenant management
        Route::resource('tenants', App\Http\Controllers\Admin\TenantController::class);

        // API token management
        Route::post('tenants/{id}/token/generate', [App\Http\Controllers\Admin\ApiTokenController::class, 'generate'])->name('tenants.token.generate');
        Route::delete('tenants/{id}/token/revoke', [App\Http\Controllers\Admin\ApiTokenController::class, 'revoke'])->name('tenants.token.revoke');

        // Feed management
        Route::resource('feeds', App\Http\Controllers\Admin\FeedController::class);
        Route::post('feeds/{id}/sync', [App\Http\Controllers\Admin\FeedController::class, 'sync'])->name('feeds.sync');
    });
});

require __DIR__.'/auth.php';
