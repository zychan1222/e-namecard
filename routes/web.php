<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialConnectionController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NamecardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\EmployeeProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and will be assigned to a
| controller. Make something great!
|
*/

Route::view('/', 'welcome');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showAdminLoginForm'])->name('login.form');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login');
    });
});

    Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'showAdminDashboard'])->name('dashboard');
        Route::get('employee/{id}', [EmployeeProfileController::class, 'viewEmployeeProfile'])->name('employee.profile');
        Route::put('employee/{id}/update', [EmployeeProfileController::class, 'update'])->name('employee.update');
    });
});

// User routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
    Route::post('register', [RegisterController::class, 'register'])->name('register');
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.show');
    Route::post('login', [LoginController::class, 'login'])->name('login');
});

// Social login routes
Route::prefix('login')->name('social.')->group(function () {
    Route::get('{provider}', [SocialConnectionController::class, 'redirectToProvider'])->name('login');
    Route::get('{provider}/callback', [SocialConnectionController::class, 'handleCallback']);
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('profile', [ProfileController::class, 'view'])->name('profile.view');
    Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('namecard', [NamecardController::class, 'showNamecard'])->name('namecard');
    Route::get('vcard-download/{employee}', [NamecardController::class, 'showVCardDownloadPage'])->name('download.vcard.page');
    Route::get('download-vcard/{name}/{phone}', [NamecardController::class, 'downloadVCard'])->name('download.vcard');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('adminlogout', [AdminAuthController::class, 'adminlogout'])->name('adminlogout');