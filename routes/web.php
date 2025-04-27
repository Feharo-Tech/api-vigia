<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\NotificationSettingController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('apis', ApiController::class);
    Route::post('/apis/{api}/check-now', [ApiController::class, 'checkNow'])->name('apis.check-now');
    Route::get('/apis/{api}/status-history', [ApiController::class, 'statusHistory'])->name('apis.status-history');
    Route::post('/apis/{api}/reset', [ApiController::class, 'reset'])->name('apis.reset');
    
    Route::get('/notification-settings/edit', [NotificationSettingController::class, 'edit'])->name('notification-settings.edit');
    Route::put('/notification-settings', [NotificationSettingController::class, 'update'])->name('notification-settings.update');
});

Route::resource('tags', \App\Http\Controllers\TagController::class)->except(['show'])->middleware('auth');

require __DIR__.'/auth.php';
