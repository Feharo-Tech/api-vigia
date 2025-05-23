<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Admin\NotificationSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\TagController;
use \App\Http\Middleware\IsAdmin;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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

    Route::resource('/tags', TagController::class)->except(['show']);
});


Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/notification-settings/edit', [NotificationSettingController::class, 'edit'])->name('admin.notification-settings.edit');
    Route::put('/notification-settings', [NotificationSettingController::class, 'update'])->name('admin.notification-settings.update');

    Route::resource('/users', UserController::class)->except(['show'])->names('admin.users');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');

    Route::resource('/certificates', CertificateController::class)->names('admin.certificates');
});

require __DIR__.'/auth.php';
