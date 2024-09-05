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
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\AdminManagementController;

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

Route::get('/', function () {
    return view('welcome');
})->middleware('log.session');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showAdminLoginForm'])->name('login.form');
        Route::post('login', [AdminAuthController::class, 'sendTAC'])->name('login.sendTAC');
        Route::get('login/tac', [AdminAuthController::class, 'showTACForm'])->name('login.tac.show');
        Route::post('login/tac', [AdminAuthController::class, 'loginWithTAC'])->name('login.tac');
        Route::get('login/{provider}', [SocialConnectionController::class, 'redirectToProvider'])->name('social.login');
        Route::get('login/{provider}/callback', [SocialConnectionController::class, 'handleCallback']);
        Route::get('/select-organization', [AdminAuthController::class, 'showOrganizationSelectionForm'])->name('select.organization');
        Route::post('/select-organization', [AdminAuthController::class, 'selectOrganization'])->name('select.organization.submit');
    
        Route::post('update-roles', [AdminDashboardController::class, 'updateRoles'])->name('update-roles');
        Route::get('dashboard', [AdminDashboardController::class, 'showAdminDashboard'])->name('dashboard');
        Route::get('dashboard/search', [AdminDashboardController::class, 'searchEmployees'])->name('dashboard.search');
        Route::get('organization', [OrganizationController::class, 'manageOrganization'])->name('organization');
        Route::put('organization/{organization}', [OrganizationController::class, 'update'])->name('organization.update');
        Route::get('employee/create', [EmployeeProfileController::class, 'create'])->name('employee.create');
        Route::post('employee', [EmployeeProfileController::class, 'store'])->name('employee.store');
        Route::delete('employee/{employee}', [EmployeeProfileController::class, 'destroy'])->name('employee.destroy');
    });

    Route::middleware(['admin', 'check.employee.company'])->group(function () {
        Route::get('employee/{employee}', [EmployeeProfileController::class, 'viewEmployeeProfile'])->name('employee.profile');
        Route::put('employee/{employee}/update', [EmployeeProfileController::class, 'update'])->name('employee.update');
    });
});

// User routes
Route::middleware('guest')->group(function () {
    // Email Registration Routes
    Route::get('register/email', [RegisterController::class, 'showEmailForm'])->name('register.email.form');
    Route::post('register/email', [RegisterController::class, 'registerEmail'])->name('register.email.store');

    // TAC Verification Route
    Route::get('verify/tac/{email}', [RegisterController::class, 'showTACForm'])->name('verify.tac.form');
    Route::post('verify/tac/{email}', [RegisterController::class, 'verifyTAC'])->name('verify.tac.store');

    // Organization Registration Routes
    Route::get('register/organization', [RegisterController::class, 'showOrganizationForm'])->name('register.organization');
    Route::post('register/organization', [RegisterController::class, 'registerOrganization'])->name('register.organization.store');

    // Admin Registration Routes
    Route::get('register/admin', [RegisterController::class, 'showAdminRegistrationForm'])->name('register.admin');
    Route::post('register/admin', [RegisterController::class, 'registerAdmin'])->name('register.admin.store');

    Route::get('/organization-created', [RegisterController::class, 'showOrganizationCreatedPage'])->name('organization.created');

    // Login Routes
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'sendTAC'])->name('login.sendTAC');
    Route::get('login/tac', [LoginController::class, 'showTACForm'])->name('login.tac.show');
    Route::post('login/tac', [LoginController::class, 'loginWithTAC'])->name('login.tac');
    Route::get('select-organization', [LoginController::class, 'showOrganizationSelectionForm'])->name('select.organization');
    Route::post('select-organization', [LoginController::class, 'selectOrganization'])->name('select.organization.post');
    // Redirect to the email registration form
    Route::get('register', function () {
        return redirect()->route('register.email.form');
    })->name('register');
});

// Social login routes
Route::prefix('login')->name('social.')->group(function () {
    Route::get('{provider}', [SocialConnectionController::class, 'redirectToProvider'])->name('login');
    Route::get('{provider}/callback', [SocialConnectionController::class, 'handleCallback']);
});

// Authenticated user routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('profile', [ProfileController::class, 'view'])->name('profile.view');
    Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('namecard', [NamecardController::class, 'showNamecard'])->name('namecard');
    Route::get('vcard-download/{employee}', [NamecardController::class, 'showVCardDownloadPage'])->name('download.vcard.page');
    Route::get('download-vcard/{name}/{phone}', [NamecardController::class, 'downloadVCard'])->name('download.vcard');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('adminlogout', [AdminAuthController::class, 'adminlogout'])->name('adminlogout');
