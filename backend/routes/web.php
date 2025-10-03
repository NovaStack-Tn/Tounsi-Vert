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
    Route::post('/events/{event}/unjoin', [ParticipationController::class, 'unjoin'])->name('events.unjoin');
    Route::post('/events/{event}/follow', [ParticipationController::class, 'follow'])->name('events.follow');
    Route::post('/events/{event}/share', [ParticipationController::class, 'share'])->name('events.share');
    
    // Organization Follow/Unfollow
    Route::post('/organizations/{organization}/follow', [OrganizationController::class, 'follow'])->name('organizations.follow');
    Route::post('/organizations/{organization}/unfollow', [OrganizationController::class, 'unfollow'])->name('organizations.unfollow');
    
    // Donations
    Route::get('/events/{event}/donate', [DonationController::class, 'create'])->name('donations.create');
    Route::post('/events/{event}/donate', [DonationController::class, 'store'])->name('donations.store');
    
    // Reviews
    Route::post('/events/{event}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('member.reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('member.reports.create');
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
    
    // Organizations (single organization per user)
    Route::get('/organizations', [OrganizerOrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/create', [OrganizerOrganizationController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [OrganizerOrganizationController::class, 'store'])->name('organizations.store');
    Route::get('/organizations/edit', [OrganizerOrganizationController::class, 'edit'])->name('organizations.edit');
    Route::put('/organizations', [OrganizerOrganizationController::class, 'update'])->name('organizations.update');
    
    // Events
    Route::resource('events', OrganizerEventController::class);
    
    // Community & Donations
    Route::get('/community', [OrganizerDashboardController::class, 'community'])->name('community');
    Route::get('/donations', [OrganizerDashboardController::class, 'donations'])->name('donations');
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
