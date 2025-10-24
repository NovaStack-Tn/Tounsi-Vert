<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ParticipationController;
use App\Http\Controllers\Member\DonationController;
use App\Http\Controllers\Member\ReviewController;
use App\Http\Controllers\Member\ReportController;
use App\Http\Controllers\Member\AIRecommendationController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Organizer\OrganizerOrganizationController;
use App\Http\Controllers\Organizer\OrganizerDashboardController;
use App\Http\Controllers\Organizer\OrganizerAiController;
use App\Http\Controllers\OrganizationRequestController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminOrganizationController;
use App\Http\Controllers\Admin\AdminOrganizationRequestController;
use App\Http\Controllers\Admin\AdminOrgCategoryController;
use App\Http\Controllers\Admin\AdminEventCategoryController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminVehiculeController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AIController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogAIController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Metrics Endpoint (Prometheus)
|--------------------------------------------------------------------------
*/

Route::get('/metrics', [MetricsController::class, 'index'])->name('metrics');

/*
|--------------------------------------------------------------------------
| Public Routes
{{ ... }}
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

// Blogs (Public)
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');

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
    
    // AI Recommendations
    Route::get('/ai/recommendations', [AIRecommendationController::class, 'index'])->name('member.ai.recommendations');
    Route::get('/api/ai/recommendations', [AIRecommendationController::class, 'getRecommendations'])->name('member.ai.api');
    
    // Organization Requests
    Route::get('/organization-request', [OrganizationRequestController::class, 'create'])->name('organization-request.create');
    Route::post('/organization-request', [OrganizationRequestController::class, 'store'])->name('organization-request.store');
    Route::get('/organization-requests', [OrganizationRequestController::class, 'index'])->name('organization-requests.index');
    
    // Blogs (Authenticated)
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');

    Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::put('/blogs/{blog}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
    
    // Blog interactions
    Route::post('/blogs/{blog}/like', [BlogController::class, 'toggleLike'])->name('blogs.like');
    Route::post('/blogs/{blog}/comments', [BlogController::class, 'storeComment'])->name('blogs.comments.store');
    Route::delete('/blogs/comments/{comment}', [BlogController::class, 'deleteComment'])->name('blogs.comments.destroy');
    
    // AI Blog Generation
    Route::post('/blogs/ai/generate-from-image', [BlogAIController::class, 'generateFromImage'])->name('blogs.ai.generateFromImage');
    Route::post('/blogs/ai/enhance-content', [BlogAIController::class, 'enhanceContent'])->name('blogs.ai.enhanceContent');
    Route::post('/blogs/ai/generate-banner', [BlogAIController::class, 'generateBannerImage'])->name('blogs.ai.generateBanner');
    
    Route::get('/my-blogs', [BlogController::class, 'myBlogs'])->name('blogs.my');
});

/*
|--------------------------------------------------------------------------
| Organizer Routes
{{ ... }}
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
    
    // Blogs
    Route::get('/blogs', [BlogController::class, 'organizerIndex'])->name('blogs.index');
    Route::get('/blogs/create', [BlogController::class, 'organizerCreate'])->name('blogs.create');
    Route::get('/blogs/{blog}', [BlogController::class, 'organizerShow'])->name('blogs.show');
    Route::get('/blogs/{blog}/edit', [BlogController::class, 'organizerEdit'])->name('blogs.edit');
    
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
    Route::get('/reports/analytics', [AdminReportController::class, 'analytics'])->name('reports.analytics');
    Route::get('/reports/advanced-analytics', [AdminReportController::class, 'advancedAnalytics'])->name('reports.advancedAnalytics');
    Route::get('/reports/search', [AdminReportController::class, 'search'])->name('reports.search');
    
    // Export routes
    Route::get('/reports/export/csv', [AdminReportController::class, 'exportCSV'])->name('reports.exportCSV');
    Route::get('/reports/export/excel', [AdminReportController::class, 'exportExcel'])->name('reports.exportExcel');
    Route::get('/reports/export/pdf', [AdminReportController::class, 'exportPDF'])->name('reports.exportPDF');
    Route::get('/reports/export/json', [AdminReportController::class, 'exportJSON'])->name('reports.exportJSON');
    
    Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/export-pdf', [AdminReportController::class, 'exportSinglePDF'])->name('reports.exportSinglePDF');
    Route::post('/reports/{report}/add-action', [AdminReportController::class, 'addAction'])->name('reports.addAction');
    Route::post('/reports/{report}/update-status', [AdminReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::post('/reports/{report}/suspend-organization', [AdminReportController::class, 'suspendOrganization'])->name('reports.suspendOrganization');
    Route::post('/reports/bulk-action', [AdminReportController::class, 'bulkAction'])->name('reports.bulkAction');
    
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
    // AI - Intelligence Artificielle
    Route::get('/ai/dashboard', [AIController::class, 'dashboard'])->name('ai.dashboard');
    Route::get('/ai/anomalies', [AIController::class, 'organizationsWithAnomalies'])->name('ai.anomalies');
    Route::get('/ai/organization/{organization}', [AIController::class, 'analyzeOrganization'])->name('ai.organization');
    Route::get('/ai/event/{event}/predict', [AIController::class, 'predictEvent'])->name('ai.predict-event');

    // Blogs Management
    Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/stats', [AdminBlogController::class, 'stats'])->name('blogs.stats');
    Route::get('/blogs/{blog}', [AdminBlogController::class, 'show'])->name('blogs.show');
    Route::delete('/blogs/{blog}', [AdminBlogController::class, 'destroy'])->name('blogs.destroy');
    Route::delete('/blog-comments/{comment}', [AdminBlogController::class, 'destroyComment'])->name('blog-comments.destroy');

});

require __DIR__.'/auth.php';
