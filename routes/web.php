<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AboutController as AdminAbout;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SermonController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\LivestreamController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TeachingController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\SermonController as AdminSermon;
use App\Http\Controllers\Admin\EventController as AdminEvent;
use App\Http\Controllers\Admin\DonationController as AdminDonation;
use App\Http\Controllers\Admin\LivestreamController as AdminLivestream;
use App\Http\Controllers\Admin\PrayerController as AdminPrayer;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncement;
use App\Http\Controllers\Admin\MemberController as AdminMember;
use App\Http\Controllers\Admin\MemberImportController;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\ChildController as AdminChild;
use App\Http\Controllers\Admin\TeachingController as AdminTeaching;
use App\Http\Controllers\Admin\ResourceController as AdminResource;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\GalleryController as AdminGallery;
use App\Http\Controllers\MemberLookupController;
use App\Http\Controllers\Admin\EventReportController as AdminEventReport;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------
// Public Routes
// -------------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::get('/sermons', [SermonController::class, 'index'])->name('sermons.index');
Route::get('/sermons/{sermon}', [SermonController::class, 'show'])->name('sermons.show');

Route::get('/teachings', [TeachingController::class, 'index'])->name('teachings.index');
Route::get('/teachings/{teaching}', [TeachingController::class, 'show'])->name('teachings.show');

Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/member-lookup', [MemberLookupController::class, 'index'])->name('member.lookup');
Route::post('/member-lookup', [MemberLookupController::class, 'lookup'])->name('member.lookup.post');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/give', [DonationController::class, 'index'])->name('give');
Route::post('/give', [DonationController::class, 'store'])->name('give.store');

Route::get('/livestream', [LivestreamController::class, 'index'])->name('livestream');

Route::get('/prayer', [PrayerController::class, 'index'])->name('prayer.index');

// -------------------------------------------------------
// Event registration — open to public (no login required)
// -------------------------------------------------------
Route::post('/events/lookup-email', [EventController::class, 'lookupEmail'])->name('events.lookup-email');
Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');

// -------------------------------------------------------
// Authenticated-only public actions
// -------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::post('/prayer', [PrayerController::class, 'store'])->name('prayer.store');
});

// -------------------------------------------------------
// Member Dashboard
// -------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -------------------------------------------------------
// Admin Routes
// -------------------------------------------------------
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Reports
        Route::get('/reports/membership',       [AdminReport::class, 'membership'])->name('reports.membership');
        Route::get('/reports/leaders',          [AdminReport::class, 'leaders'])->name('reports.leaders');
        Route::get('/reports/mentorship',       [AdminReport::class, 'mentorship'])->name('reports.mentorship');
        Route::get('/reports/children-parent',  [AdminReport::class, 'childrenByParent'])->name('reports.children-parent');
        Route::get('/reports/by-department',    [AdminReport::class, 'byDepartment'])->name('reports.by-department');

        // Event Reports
        Route::get('/reports/events',                    [AdminEventReport::class, 'index'])->name('reports.events');
        Route::get('/reports/events/{event}',            [AdminEventReport::class, 'show'])->name('reports.events.show');

        // Children
        Route::get('/children/import',  [AdminChild::class, 'importForm'])->name('children.import');
        Route::post('/children/import', [AdminChild::class, 'importStore'])->name('children.import.store');
        Route::get('/children/print',   [AdminChild::class, 'printList'])->name('children.print');
        Route::resource('children', AdminChild::class);

        // Sermons CRUD
        Route::resource('sermons', AdminSermon::class);

        // Teachings CRUD
        Route::resource('teachings', AdminTeaching::class);

        // Resources (PDF books & articles) CRUD
        Route::resource('resources', AdminResource::class);

        // Gallery
        Route::resource('gallery', AdminGallery::class);

        // Events CRUD
        Route::resource('events', AdminEvent::class);

        // Donations (read + status update only)
        Route::get('/donations', [AdminDonation::class, 'index'])->name('donations.index');
        Route::get('/donations/{donation}', [AdminDonation::class, 'show'])->name('donations.show');
        Route::patch('/donations/{donation}/status', [AdminDonation::class, 'updateStatus'])->name('donations.updateStatus');

        // Livestreams CRUD + toggle
        Route::resource('livestreams', AdminLivestream::class);
        Route::post('/livestreams/{livestream}/toggle-live', [AdminLivestream::class, 'toggleLive'])->name('livestreams.toggleLive');

        // Prayer Requests
        Route::get('/prayers', [AdminPrayer::class, 'index'])->name('prayers.index');
        Route::patch('/prayers/{prayer}/status', [AdminPrayer::class, 'updateStatus'])->name('prayers.updateStatus');

        // Announcements CRUD
        Route::resource('announcements', AdminAnnouncement::class);

        // About Us
        Route::get('/about', [AdminAbout::class, 'index'])->name('about.index');
        Route::post('/about/info', [AdminAbout::class, 'updateInfo'])->name('about.updateInfo');
        Route::post('/about/leaders', [AdminAbout::class, 'storeLeader'])->name('about.leaders.store');
        Route::post('/about/leaders/{leader}', [AdminAbout::class, 'updateLeader'])->name('about.leaders.update');
        Route::delete('/about/leaders/{leader}', [AdminAbout::class, 'destroyLeader'])->name('about.leaders.destroy');
        Route::post('/about/pillars', [AdminAbout::class, 'storePillar'])->name('about.pillars.store');
        Route::post('/about/pillars/{pillar}', [AdminAbout::class, 'updatePillar'])->name('about.pillars.update');
        Route::delete('/about/pillars/{pillar}', [AdminAbout::class, 'destroyPillar'])->name('about.pillars.destroy');

        // Members
        Route::get('/members/print', [AdminMember::class, 'printList'])->name('members.print');
        Route::get('/members/import', [MemberImportController::class, 'showForm'])->name('members.import');
        Route::post('/members/import', [MemberImportController::class, 'import'])->name('members.import.store');
        Route::get('/members', [AdminMember::class, 'index'])->name('members.index');
        Route::get('/members/report', [AdminMember::class, 'report'])->name('members.report');
        Route::get('/members/create', [AdminMember::class, 'create'])->name('members.create');
        Route::post('/members', [AdminMember::class, 'store'])->name('members.store');
        Route::get('/members/{user}', [AdminMember::class, 'show'])->name('members.show');
        Route::get('/members/{user}/edit', [AdminMember::class, 'edit'])->name('members.edit');
        Route::put('/members/{user}', [AdminMember::class, 'update'])->name('members.update');
        Route::patch('/members/{user}/role', [AdminMember::class, 'updateRole'])->name('members.updateRole');
    });

require __DIR__.'/auth.php';
