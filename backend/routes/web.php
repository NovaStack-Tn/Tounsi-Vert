<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Member\ParticipationController;
use App\Http\Controllers\Member\DonationController;
use App\Http\Controllers\Member\ReviewController;
use App\Http\Controllers\Member\ReportController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Organizer\OrganizerOrganizationController;
use App\Http\Controllers\Organizer\OrganizerDashboardController;
use App\Http\Controllers\Organizer\OrganizerAiController;
use App\Http\Controllers\OrganizationRequestController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrganizationController;
use App\Http\Controllers\Admin\AdminVehiculeController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminOrganizationRequestController;
use App\Http\Controllers\Admin\AdminOrgCategoryController;
use App\Http\Controllers\Admin\AdminEventCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

//VolunteerRides and booking
Route::post('/bookings/quick', [BookingController::class, 'quickMatch'])->name('bookings.quickMatch');
Route::get('bookings/quick', [BookingController::class, 'quickForm'])->name('bookings.quickForm');
Route::post('bookings/quick', [BookingController::class, 'quickMatch'])->name('bookings.quickMatch');
Route::resource('bookings', BookingController::class);
Route::resource('vehicules', VehiculeController::class);
Route::resource('bookings', BookingController::class);
Route::get('/vehicules/{vehicule}/confirm', [VehiculeController::class, 'confirm'])->name('vehicules.confirm');



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
    
    // AI Insights
    Route::get('/ai', [OrganizerAiController::class, 'index'])->name('ai');
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
    Route::get('/organizations/export', [AdminOrganizationController::class, 'export'])->name('organizations.export');
    Route::get('/organizations/{organization}/insights', [AdminOrganizationController::class, 'insights'])->name('organizations.insights');
    Route::post('/organizations/{organization}/verify', [AdminOrganizationController::class, 'verify'])->name('organizations.verify');
    Route::post('/organizations/{organization}/unverify', [AdminOrganizationController::class, 'unverify'])->name('organizations.unverify');
    Route::post('/organizations/bulk-verify', [AdminOrganizationController::class, 'bulkVerify'])->name('organizations.bulk-verify');
    Route::post('/organizations/bulk-unverify', [AdminOrganizationController::class, 'bulkUnverify'])->name('organizations.bulk-unverify');
    Route::post('/organizations/bulk-reject', [AdminOrganizationController::class, 'bulkReject'])->name('organizations.bulk-reject');
    Route::delete('/organizations/{organization}', [AdminOrganizationController::class, 'destroy'])->name('organizations.destroy');
    
    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/export', [AdminEventController::class, 'export'])->name('events.export');
    Route::get('/events/leaderboard', [AdminEventController::class, 'leaderboard'])->name('events.leaderboard');
    Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/ics', [AdminEventController::class, 'exportIcs'])->name('events.ics');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    
    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/dismiss', [AdminReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::post('/reports/{report}/suspend-organization', [AdminReportController::class, 'suspendOrganization'])->name('reports.suspendOrganization');
    Route::patch('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.updateStatus');
    
    // Organization Requests
    Route::get('/organization-requests', [AdminOrganizationRequestController::class, 'index'])->name('organization-requests.index');
    Route::get('/organization-requests/{organizationRequest}', [AdminOrganizationRequestController::class, 'show'])->name('organization-requests.show');
    Route::post('/organization-requests/{organizationRequest}/approve', [AdminOrganizationRequestController::class, 'approve'])->name('organization-requests.approve');
    Route::post('/organization-requests/{organizationRequest}/reject', [AdminOrganizationRequestController::class, 'reject'])->name('organization-requests.reject');
     // Vehicules
    Route::get('/vehicules', [AdminVehiculeController::class, 'index'])->name('vehicules.index');
    Route::post('/vehicules/{vehicule}/verify', [AdminVehiculeController::class, 'verify'])->name('vehicules.verify');
    Route::post('/vehicules/{vehicule}/unverify', [AdminVehiculeController::class, 'unverify'])->name('vehicules.unverify');
    Route::delete('/vehicules/{vehicule}', [AdminVehiculeController::class, 'destroy'])->name('vehicules.destroy');

    // Organization Categories
    Route::get('/org-categories', [AdminOrgCategoryController::class, 'index'])->name('org-categories.index');
    Route::get('/org-categories/create', [AdminOrgCategoryController::class, 'create'])->name('org-categories.create');
    Route::post('/org-categories', [AdminOrgCategoryController::class, 'store'])->name('org-categories.store');
    Route::get('/org-categories/{orgCategory}/edit', [AdminOrgCategoryController::class, 'edit'])->name('org-categories.edit');
    Route::put('/org-categories/{orgCategory}', [AdminOrgCategoryController::class, 'update'])->name('org-categories.update');
    Route::delete('/org-categories/{orgCategory}', [AdminOrgCategoryController::class, 'destroy'])->name('org-categories.destroy');

    // Event Categories
    Route::get('/event-categories', [AdminEventCategoryController::class, 'index'])->name('event-categories.index');
    Route::get('/event-categories/create', [AdminEventCategoryController::class, 'create'])->name('event-categories.create');
    Route::post('/event-categories', [AdminEventCategoryController::class, 'store'])->name('event-categories.store');
    Route::get('/event-categories/{eventCategory}/edit', [AdminEventCategoryController::class, 'edit'])->name('event-categories.edit');
    Route::put('/event-categories/{eventCategory}', [AdminEventCategoryController::class, 'update'])->name('event-categories.update');
    Route::delete('/event-categories/{eventCategory}', [AdminEventCategoryController::class, 'destroy'])->name('event-categories.destroy');
});

require __DIR__.'/auth.php';
