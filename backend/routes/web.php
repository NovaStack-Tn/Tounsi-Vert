<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Member\ParticipationController;
use App\Http\Controllers\Member\DonationController;
use App\Http\Controllers\Member\ReviewController;
use App\Http\Controllers\Member\ReportController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Organizer\OrganizerOrganizationController;
use App\Http\Controllers\Organizer\OrganizerDashboardController;
use App\Http\Controllers\OrganizationRequestController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrganizationController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminOrganizationRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Events
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Organizations
Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');

/*
|--------------------------------------------------------------------------
| Member Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Member Dashboard
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    
    // Participations
    Route::post('/events/{event}/attend', [ParticipationController::class, 'attend'])->name('events.attend');
    Route::post('/events/{event}/follow', [ParticipationController::class, 'follow'])->name('events.follow');
    Route::post('/events/{event}/share', [ParticipationController::class, 'share'])->name('events.share');
    
    // Organization Follow/Unfollow
    Route::post('/organizations/{organization}/follow', [OrganizationController::class, 'follow'])->name('organizations.follow');
    Route::post('/organizations/{organization}/unfollow', [OrganizationController::class, 'unfollow'])->name('organizations.unfollow');
    
    // Donations - Full CRUD
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/statistics', [DonationController::class, 'statistics'])->name('donations.statistics');
    Route::get('/donations/create', [DonationController::class, 'create'])->name('donations.create');
    Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');
    Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    Route::get('/donations/{donation}/edit', [DonationController::class, 'edit'])->name('donations.edit');
    Route::put('/donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
    Route::delete('/donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
    
    // Donation Export
    Route::get('/donations-export/pdf', [DonationController::class, 'exportPdf'])->name('donations.export.pdf');
    Route::get('/donations-export/csv', [DonationController::class, 'exportCsv'])->name('donations.export.csv');
    
    // Legacy event donation routes (for backwards compatibility)
    Route::get('/events/{event}/donate', [DonationController::class, 'createForEvent'])->name('events.donate.create');
    Route::post('/events/{event}/donate', [DonationController::class, 'storeForEvent'])->name('events.donate.store');
    
    // Reviews
    Route::post('/events/{event}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    
    // Reports
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    
    // Organization Requests
    Route::get('/organization-request', [OrganizationRequestController::class, 'create'])->name('organization-request.create');
    Route::post('/organization-request', [OrganizationRequestController::class, 'store'])->name('organization-request.store');
    Route::get('/organization-requests', [OrganizationRequestController::class, 'index'])->name('organization-requests.index');
});

/*
|--------------------------------------------------------------------------
| Organizer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('organizer')->name('organizer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');
    
    // Organizations
    Route::resource('organizations', OrganizerOrganizationController::class);
    
    // Events
    Route::resource('events', OrganizerEventController::class);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Organizations
    Route::get('/organizations', [AdminOrganizationController::class, 'index'])->name('organizations.index');
    Route::post('/organizations/{organization}/verify', [AdminOrganizationController::class, 'verify'])->name('organizations.verify');
    Route::post('/organizations/{organization}/unverify', [AdminOrganizationController::class, 'unverify'])->name('organizations.unverify');
    Route::delete('/organizations/{organization}', [AdminOrganizationController::class, 'destroy'])->name('organizations.destroy');
    
    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.updateStatus');
    
    // Organization Requests
    Route::get('/organization-requests', [AdminOrganizationRequestController::class, 'index'])->name('organization-requests.index');
    Route::get('/organization-requests/{organizationRequest}', [AdminOrganizationRequestController::class, 'show'])->name('organization-requests.show');
    Route::post('/organization-requests/{organizationRequest}/approve', [AdminOrganizationRequestController::class, 'approve'])->name('organization-requests.approve');
    Route::post('/organization-requests/{organizationRequest}/reject', [AdminOrganizationRequestController::class, 'reject'])->name('organization-requests.reject');
});

require __DIR__.'/auth.php';
