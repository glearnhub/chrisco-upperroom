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
use App\Http\Controllers\Admin\ChildAttendanceController as AdminChildAttendance;
use App\Http\Controllers\Admin\VisitorController as AdminVisitor;
use App\Http\Controllers\Admin\TeachingController as AdminTeaching;
use App\Http\Controllers\Admin\ResourceController as AdminResource;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\GalleryController as AdminGallery;
use App\Http\Controllers\MemberLookupController;
use App\Http\Controllers\Admin\EventReportController as AdminEventReport;
use App\Http\Controllers\Admin\CorrectionRequestController as AdminCorrection;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Admin\Settings\SocialMediaController as AdminSocial;
use App\Http\Controllers\Admin\Settings\VerifyAccessController as AdminVerifyAccess;
use App\Http\Controllers\Admin\Settings\SystemUserController as AdminSysUser;
use App\Http\Controllers\Admin\Settings\RolePermissionController as AdminRolePerms;
use App\Http\Controllers\Admin\Settings\SystemLogController as AdminSysLog;
use App\Http\Controllers\ApostleTeachingController;
use App\Http\Controllers\Admin\ApostleTeachingController as AdminApostleTeaching;
use App\Http\Controllers\Admin\ApostleTeachingCategoryController as AdminApostleCategory;
use App\Http\Controllers\Admin\ApostleTeachingImportController as AdminApostleImport;
use App\Http\Controllers\ChurchCalendarController;
use App\Http\Controllers\Admin\ChurchCalendarController as AdminCalendar;
use App\Http\Controllers\Admin\ChurchCalendarImportController as AdminCalendarImport;
use App\Http\Controllers\Admin\SermonImportController as AdminSermonImport;
use App\Http\Controllers\Admin\MyProfileController as AdminMyProfile;
use App\Http\Controllers\Admin\ForcePasswordChangeController as AdminForcePassword;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EventAttendanceController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendance;
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

Route::get('/apostle-teachings', [ApostleTeachingController::class, 'index'])->name('apostle.index');
Route::get('/apostle-teachings/{apostleTeaching}', [ApostleTeachingController::class, 'show'])->name('apostle.show');

Route::middleware(['verify.access', 'throttle:20,1'])->group(function () {
    Route::get('/verify', [MemberLookupController::class, 'index'])->name('member.lookup');
    Route::post('/verify', [MemberLookupController::class, 'lookup'])->name('member.lookup.post');
    Route::post('/verify/correction', [MemberLookupController::class, 'requestCorrection'])->name('member.lookup.correction');
});

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/give', [DonationController::class, 'index'])->name('give');
Route::post('/give', [DonationController::class, 'store'])->name('give.store');

Route::get('/livestream', [LivestreamController::class, 'index'])->name('livestream');

Route::get('/prayer', [PrayerController::class, 'index'])->name('prayer.index');

Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');

Route::get('/calendar', [ChurchCalendarController::class, 'index'])->name('church.calendar');
Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

// -------------------------------------------------------
// Event registration — open to public (no login required)
// -------------------------------------------------------
Route::post('/events/lookup-email', [EventController::class, 'lookupEmail'])->name('events.lookup-email')->middleware('throttle:20,1');
Route::post('/events/send-otp',    [EventController::class, 'sendOtp'])->name('events.send-otp')->middleware('throttle:5,10');
Route::post('/events/verify-otp',  [EventController::class, 'verifyOtp'])->name('events.verify-otp')->middleware('throttle:10,1');
Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');

Route::post('/prayer', [PrayerController::class, 'store'])->name('prayer.store');

// -------------------------------------------------------
// QR code image generator (public)
Route::get('/qr', [QrCodeController::class, 'generate'])->name('qr.generate')->middleware('throttle:120,1');

// Sunday Check-In (public — no login required)
// -------------------------------------------------------
Route::get('/attend', [AttendanceController::class, 'show'])->name('attend.show');
Route::post('/attend/search', [AttendanceController::class, 'search'])->name('attend.search')->middleware('throttle:60,1');
Route::post('/attend/checkin', [AttendanceController::class, 'checkin'])->name('attend.checkin')->middleware('throttle:30,1');

// -------------------------------------------------------
// Event Check-In (public — no login required)
// -------------------------------------------------------
Route::get('/attend/event/{event}',         [EventAttendanceController::class, 'show'])->name('attend.event.show');
Route::post('/attend/event/{event}/search', [EventAttendanceController::class, 'search'])->name('attend.event.search')->middleware('throttle:60,1');
Route::post('/attend/event/{event}/checkin',[EventAttendanceController::class, 'checkin'])->name('attend.event.checkin')->middleware('throttle:30,1');
Route::post('/attend/event/{event}/walkin', [EventAttendanceController::class, 'walkin'])->name('attend.event.walkin')->middleware('throttle:20,1');

// -------------------------------------------------------
// Member Dashboard
// -------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// /admin returns 404 — keeps the real login URL hidden
Route::get('/admin', fn() => abort(404))->withoutMiddleware(['auth', 'admin']);

// -------------------------------------------------------
// Admin Routes
// -------------------------------------------------------
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Force password change (no force.password.change middleware here — these ARE the escape routes)
        Route::get('/force-password-change',   [AdminForcePassword::class, 'show'])->name('password.force.show');
        Route::patch('/force-password-change', [AdminForcePassword::class, 'update'])->name('password.force.update');

        // All other admin routes require password to not need changing
        Route::middleware('force.password.change')->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Reports
        Route::get('/reports/membership',      [AdminReport::class, 'membership'])->middleware('permission:reports.membership')->name('reports.membership');
        Route::get('/reports/leaders',         [AdminReport::class, 'leaders'])->middleware('permission:reports.membership')->name('reports.leaders');
        Route::get('/reports/mentorship',      [AdminReport::class, 'mentorship'])->middleware('permission:reports.membership')->name('reports.mentorship');
        Route::get('/reports/children-parent', [AdminReport::class, 'childrenByParent'])->middleware('permission:reports.children')->name('reports.children-parent');
        Route::get('/reports/by-department',   [AdminReport::class, 'byDepartment'])->middleware('permission:reports.membership')->name('reports.by-department');
        Route::get('/reports/committed',       [AdminReport::class, 'committed'])->middleware('permission:reports.membership')->name('reports.committed');
        Route::get('/reports/in-commitment',   [AdminReport::class, 'inCommitment'])->middleware('permission:reports.membership')->name('reports.in-commitment');
        Route::get('/reports/young-converts',  [AdminReport::class, 'youngConverts'])->middleware('permission:reports.membership')->name('reports.young-converts');
        Route::get('/reports/not-baptised',    [AdminReport::class, 'notBaptised'])->middleware('permission:reports.membership')->name('reports.not-baptised');
        Route::get('/reports/married',         [AdminReport::class, 'married'])->middleware('permission:reports.membership')->name('reports.married');
        Route::get('/reports/pearls',          [AdminReport::class, 'pearls'])->middleware('permission:reports.membership')->name('reports.pearls');
        Route::get('/reports/singles-youths',   [AdminReport::class, 'singlesYouths'])->middleware('permission:reports.membership')->name('reports.singles-youths');
        Route::get('/reports/transferred-in',   [AdminReport::class, 'transferredIn'])->middleware('permission:reports.membership')->name('reports.transferred-in');
        Route::get('/reports/transferred-out',  [AdminReport::class, 'transferredOut'])->middleware('permission:reports.membership')->name('reports.transferred-out');
        Route::get('/reports/active-members',   [AdminReport::class, 'activeMembers'])->middleware('permission:reports.membership')->name('reports.active-members');
        Route::get('/reports/inactive-members', [AdminReport::class, 'inactiveMembers'])->middleware('permission:reports.membership')->name('reports.inactive-members');
        Route::get('/reports/events',                                              [AdminEventReport::class, 'index'])->middleware('permission:reports.events')->name('reports.events');
        Route::get('/reports/events/{event}',                                      [AdminEventReport::class, 'show'])->middleware('permission:reports.events')->name('reports.events.show');
        Route::get('/reports/events/{event}/attendance/search',                    [AdminEventReport::class, 'searchAttendee'])->middleware('permission:reports.events')->name('reports.events.attendance.search');
        Route::post('/reports/events/{event}/attendance/{registration}',           [AdminEventReport::class, 'markAttendance'])->middleware('permission:reports.events')->name('reports.events.attendance.mark');

        // Visitors
        Route::get('/visitors/import/template', [AdminVisitor::class, 'downloadTemplate'])->middleware('permission:visitors.view')->name('visitors.import.template');
        Route::get('/visitors/import',  [AdminVisitor::class, 'importForm'])->middleware('permission:visitors.create')->name('visitors.import');
        Route::post('/visitors/import', [AdminVisitor::class, 'importStore'])->middleware('permission:visitors.create')->name('visitors.import.store');
        Route::get('/visitors',         [AdminVisitor::class, 'index'])->middleware('permission:visitors.view')->name('visitors.index');
        Route::get('/visitors/create',  [AdminVisitor::class, 'create'])->middleware('permission:visitors.create')->name('visitors.create');
        Route::post('/visitors',        [AdminVisitor::class, 'store'])->middleware('permission:visitors.create')->name('visitors.store');
        Route::get('/visitors/{visitor}',      [AdminVisitor::class, 'show'])->middleware('permission:visitors.view')->name('visitors.show');
        Route::get('/visitors/{visitor}/edit', [AdminVisitor::class, 'edit'])->middleware('permission:visitors.edit')->name('visitors.edit');
        Route::match(['PUT','PATCH'], '/visitors/{visitor}', [AdminVisitor::class, 'update'])->middleware('permission:visitors.edit')->name('visitors.update');
        Route::delete('/visitors/{visitor}',   [AdminVisitor::class, 'destroy'])->middleware('permission:visitors.delete')->name('visitors.destroy');

        // Child Attendance
        Route::get('/children/attendance',              [AdminChildAttendance::class, 'scanner'])->middleware('permission:children.view')->name('children.attendance');
        Route::get('/children/attendance/history',      [AdminChildAttendance::class, 'history'])->middleware('permission:children.view')->name('children.attendance.history');
        Route::get('/children/attendance/report',       [AdminChildAttendance::class, 'report'])->middleware('permission:children.view')->name('children.attendance.report');
        Route::get('/children/attendance/descriptors',  [AdminChildAttendance::class, 'descriptors'])->middleware('permission:children.view')->name('children.attendance.descriptors');
        Route::post('/children/attendance/save',        [AdminChildAttendance::class, 'saveAttendance'])->middleware('permission:children.create')->name('children.attendance.save');
        Route::delete('/children/attendance/{attendance}', [AdminChildAttendance::class, 'remove'])->middleware('permission:children.edit')->name('children.attendance.remove');
        Route::post('/children/{child}/save-descriptor', [AdminChildAttendance::class, 'saveDescriptor'])->middleware('permission:children.edit')->name('children.save-descriptor');

        // Children
        Route::get('/children/print',   [AdminChild::class, 'printList'])->middleware('permission:children.view')->name('children.print');
        Route::get('/children/import',  [AdminChild::class, 'importForm'])->middleware('permission:children.create')->name('children.import');
        Route::post('/children/import', [AdminChild::class, 'importStore'])->middleware('permission:children.create')->name('children.import.store');
        Route::get('/children',         [AdminChild::class, 'index'])->middleware('permission:children.view')->name('children.index');
        Route::get('/children/create',  [AdminChild::class, 'create'])->middleware('permission:children.create')->name('children.create');
        Route::post('/children',        [AdminChild::class, 'store'])->middleware('permission:children.create')->name('children.store');
        Route::get('/children/{child}', [AdminChild::class, 'show'])->middleware('permission:children.view')->name('children.show');
        Route::get('/children/{child}/edit', [AdminChild::class, 'edit'])->middleware('permission:children.edit')->name('children.edit');
        Route::match(['PUT','PATCH'], '/children/{child}', [AdminChild::class, 'update'])->middleware('permission:children.edit')->name('children.update');
        Route::delete('/children/{child}',   [AdminChild::class, 'destroy'])->middleware('permission:children.delete')->name('children.destroy');

        // Sermons
        Route::get('/sermons/import',          [AdminSermonImport::class, 'showForm'])->middleware('permission:sermons.create')->name('sermons.import');
        Route::post('/sermons/import',         [AdminSermonImport::class, 'import'])->middleware('permission:sermons.create')->name('sermons.import.store');
        Route::get('/sermons/import/template', [AdminSermonImport::class, 'downloadTemplate'])->middleware('permission:sermons.view')->name('sermons.import.template');
        Route::get('/sermons',               [AdminSermon::class, 'index'])->middleware('permission:sermons.view')->name('sermons.index');
        Route::get('/sermons/create',        [AdminSermon::class, 'create'])->middleware('permission:sermons.create')->name('sermons.create');
        Route::post('/sermons',              [AdminSermon::class, 'store'])->middleware('permission:sermons.create')->name('sermons.store');
        Route::get('/sermons/{sermon}',      [AdminSermon::class, 'show'])->middleware('permission:sermons.view')->name('sermons.show');
        Route::get('/sermons/{sermon}/edit', [AdminSermon::class, 'edit'])->middleware('permission:sermons.edit')->name('sermons.edit');
        Route::match(['PUT','PATCH'], '/sermons/{sermon}', [AdminSermon::class, 'update'])->middleware('permission:sermons.edit')->name('sermons.update');
        Route::delete('/sermons/{sermon}',   [AdminSermon::class, 'destroy'])->middleware('permission:sermons.delete')->name('sermons.destroy');

        // Teachings
        Route::get('/teachings',                 [AdminTeaching::class, 'index'])->middleware('permission:teachings.view')->name('teachings.index');
        Route::get('/teachings/create',          [AdminTeaching::class, 'create'])->middleware('permission:teachings.create')->name('teachings.create');
        Route::post('/teachings',                [AdminTeaching::class, 'store'])->middleware('permission:teachings.create')->name('teachings.store');
        Route::get('/teachings/{teaching}',      [AdminTeaching::class, 'show'])->middleware('permission:teachings.view')->name('teachings.show');
        Route::get('/teachings/{teaching}/edit', [AdminTeaching::class, 'edit'])->middleware('permission:teachings.edit')->name('teachings.edit');
        Route::match(['PUT','PATCH'], '/teachings/{teaching}', [AdminTeaching::class, 'update'])->middleware('permission:teachings.edit')->name('teachings.update');
        Route::delete('/teachings/{teaching}',   [AdminTeaching::class, 'destroy'])->middleware('permission:teachings.delete')->name('teachings.destroy');

        // Resources
        Route::get('/resources',                 [AdminResource::class, 'index'])->middleware('permission:resources.view')->name('resources.index');
        Route::get('/resources/create',          [AdminResource::class, 'create'])->middleware('permission:resources.create')->name('resources.create');
        Route::post('/resources',                [AdminResource::class, 'store'])->middleware('permission:resources.create')->name('resources.store');
        Route::get('/resources/{resource}',      [AdminResource::class, 'show'])->middleware('permission:resources.view')->name('resources.show');
        Route::get('/resources/{resource}/edit', [AdminResource::class, 'edit'])->middleware('permission:resources.edit')->name('resources.edit');
        Route::match(['PUT','PATCH'], '/resources/{resource}', [AdminResource::class, 'update'])->middleware('permission:resources.edit')->name('resources.update');
        Route::delete('/resources/{resource}',   [AdminResource::class, 'destroy'])->middleware('permission:resources.delete')->name('resources.destroy');

        // Gallery
        Route::get('/gallery',               [AdminGallery::class, 'index'])->middleware('permission:gallery.view')->name('gallery.index');
        Route::get('/gallery/create',        [AdminGallery::class, 'create'])->middleware('permission:gallery.create')->name('gallery.create');
        Route::post('/gallery',              [AdminGallery::class, 'store'])->middleware('permission:gallery.create')->name('gallery.store');
        Route::get('/gallery/{gallery}',      [AdminGallery::class, 'show'])->middleware('permission:gallery.view')->name('gallery.show');
        Route::get('/gallery/{gallery}/edit', [AdminGallery::class, 'edit'])->middleware('permission:gallery.edit')->name('gallery.edit');
        Route::match(['PUT','PATCH'], '/gallery/{gallery}', [AdminGallery::class, 'update'])->middleware('permission:gallery.edit')->name('gallery.update');
        Route::delete('/gallery/{gallery}',   [AdminGallery::class, 'destroy'])->middleware('permission:gallery.delete')->name('gallery.destroy');

        // Apostle Das Teachings
        Route::get('/apostle/import',            [AdminApostleImport::class, 'showForm'])->middleware('permission:sermons.create')->name('apostle.import');
        Route::post('/apostle/import',           [AdminApostleImport::class, 'import'])->middleware('permission:sermons.create')->name('apostle.import.store');
        Route::get('/apostle/import/template',   [AdminApostleImport::class, 'downloadTemplate'])->middleware('permission:sermons.view')->name('apostle.import.template');
        Route::get('/apostle',                   [AdminApostleTeaching::class, 'index'])->middleware('permission:sermons.view')->name('apostle.index');
        Route::get('/apostle/create',            [AdminApostleTeaching::class, 'create'])->middleware('permission:sermons.create')->name('apostle.create');
        Route::post('/apostle',                  [AdminApostleTeaching::class, 'store'])->middleware('permission:sermons.create')->name('apostle.store');
        // Categories must come before the wildcard {apostleTeaching} routes
        Route::get('/apostle/categories',                            [AdminApostleCategory::class, 'index'])->middleware('permission:sermons.view')->name('apostle.categories.index');
        Route::post('/apostle/categories',                           [AdminApostleCategory::class, 'store'])->middleware('permission:sermons.create')->name('apostle.categories.store');
        Route::match(['PUT','PATCH'], '/apostle/categories/{apostleTeachingCategory}', [AdminApostleCategory::class, 'update'])->middleware('permission:sermons.edit')->name('apostle.categories.update');
        Route::delete('/apostle/categories/{apostleTeachingCategory}', [AdminApostleCategory::class, 'destroy'])->middleware('permission:sermons.delete')->name('apostle.categories.destroy');
        Route::get('/apostle/{apostleTeaching}/edit', [AdminApostleTeaching::class, 'edit'])->middleware('permission:sermons.edit')->name('apostle.edit');
        Route::match(['PUT','PATCH'], '/apostle/{apostleTeaching}', [AdminApostleTeaching::class, 'update'])->middleware('permission:sermons.edit')->name('apostle.update');
        Route::delete('/apostle/{apostleTeaching}',   [AdminApostleTeaching::class, 'destroy'])->middleware('permission:sermons.delete')->name('apostle.destroy');

        // Events
        Route::get('/events',              [AdminEvent::class, 'index'])->middleware('permission:events.view')->name('events.index');
        Route::get('/events/create',       [AdminEvent::class, 'create'])->middleware('permission:events.create')->name('events.create');
        Route::post('/events',             [AdminEvent::class, 'store'])->middleware('permission:events.create')->name('events.store');
        Route::get('/events/{event}',      [AdminEvent::class, 'show'])->middleware('permission:events.view')->name('events.show');
        Route::get('/events/{event}/edit', [AdminEvent::class, 'edit'])->middleware('permission:events.edit')->name('events.edit');
        Route::match(['PUT','PATCH'], '/events/{event}', [AdminEvent::class, 'update'])->middleware('permission:events.edit')->name('events.update');
        Route::delete('/events/{event}',   [AdminEvent::class, 'destroy'])->middleware('permission:events.delete')->name('events.destroy');
        // Event check-in usher panel
        Route::get('/events/{event}/checkin',                [AdminEvent::class, 'checkin'])->middleware('permission:attendance.view')->name('events.checkin');
        Route::get('/events/{event}/checkin/search',         [AdminEvent::class, 'checkinSearch'])->middleware('permission:attendance.view')->name('events.checkin.search');
        Route::post('/events/{event}/checkin/mark/{registration}', [AdminEvent::class, 'checkinMark'])->middleware('permission:attendance.manage')->name('events.checkin.mark');
        Route::post('/events/{event}/checkin/walkin',        [AdminEvent::class, 'checkinWalkin'])->middleware('permission:attendance.manage')->name('events.checkin.walkin');

        // Donations
        Route::get('/donations',                          [AdminDonation::class, 'index'])->middleware('permission:givings.view')->name('donations.index');
        Route::get('/donations/{donation}',               [AdminDonation::class, 'show'])->middleware('permission:givings.view')->name('donations.show');
        Route::patch('/donations/{donation}/status',      [AdminDonation::class, 'updateStatus'])->middleware('permission:givings.manage')->name('donations.updateStatus');

        // Livestreams
        Route::get('/livestreams',                        [AdminLivestream::class, 'index'])->middleware('permission:livestream.view')->name('livestreams.index');
        Route::get('/livestreams/create',                 [AdminLivestream::class, 'create'])->middleware('permission:livestream.manage')->name('livestreams.create');
        Route::post('/livestreams',                       [AdminLivestream::class, 'store'])->middleware('permission:livestream.manage')->name('livestreams.store');
        Route::get('/livestreams/{livestream}',           [AdminLivestream::class, 'show'])->middleware('permission:livestream.view')->name('livestreams.show');
        Route::get('/livestreams/{livestream}/edit',      [AdminLivestream::class, 'edit'])->middleware('permission:livestream.manage')->name('livestreams.edit');
        Route::match(['PUT','PATCH'], '/livestreams/{livestream}', [AdminLivestream::class, 'update'])->middleware('permission:livestream.manage')->name('livestreams.update');
        Route::delete('/livestreams/{livestream}',        [AdminLivestream::class, 'destroy'])->middleware('permission:livestream.manage')->name('livestreams.destroy');
        Route::post('/livestreams/{livestream}/toggle-live', [AdminLivestream::class, 'toggleLive'])->middleware('permission:livestream.manage')->name('livestreams.toggleLive');
        Route::post('/livestreams/end-all',               [AdminLivestream::class, 'endAll'])->middleware('permission:livestream.manage')->name('livestreams.endAll');

        // Church Calendar
        Route::get('/calendar',                    [AdminCalendar::class, 'index'])->name('calendar.index');
        Route::get('/calendar/create',             [AdminCalendar::class, 'create'])->name('calendar.create');
        Route::post('/calendar',                   [AdminCalendar::class, 'store'])->name('calendar.store');
        Route::get('/calendar/print',              [AdminCalendar::class, 'print'])->name('calendar.print');
        Route::get('/calendar/import',             [AdminCalendarImport::class, 'showForm'])->name('calendar.import.form');
        Route::post('/calendar/import',            [AdminCalendarImport::class, 'import'])->name('calendar.import.store');
        Route::get('/calendar/import/template',    [AdminCalendarImport::class, 'downloadTemplate'])->name('calendar.import.template');
        Route::get('/calendar/{calendar}/edit',         [AdminCalendar::class, 'edit'])->name('calendar.edit');
        Route::put('/calendar/{calendar}',              [AdminCalendar::class, 'update'])->name('calendar.update');
        Route::delete('/calendar/{calendar}',           [AdminCalendar::class, 'destroy'])->name('calendar.destroy');
        Route::patch('/calendar/{calendar}/visibility', [AdminCalendar::class, 'toggleVisibility'])->name('calendar.toggle-visibility');
        Route::get('/calendar/{calendar}/publish-event',  [AdminCalendar::class, 'publishEventForm'])->name('calendar.publish-event.form');
        Route::post('/calendar/{calendar}/publish-event', [AdminCalendar::class, 'publishEventStore'])->name('calendar.publish-event.store');

        // Prayer Requests
        Route::get('/prayers',                       [AdminPrayer::class, 'index'])->middleware('permission:prayers.view')->name('prayers.index');
        Route::post('/prayers/bulk-assign',          [AdminPrayer::class, 'bulkAssign'])->middleware('permission:prayers.manage')->name('prayers.bulk-assign');
        Route::patch('/prayers/{prayer}/status',     [AdminPrayer::class, 'updateStatus'])->middleware('permission:prayers.manage')->name('prayers.updateStatus');
        Route::patch('/prayers/{prayer}/assign',     [AdminPrayer::class, 'assign'])->middleware('permission:prayers.manage')->name('prayers.assign');

        // Announcements
        Route::get('/announcements',                                    [AdminAnnouncement::class, 'index'])->middleware('permission:announcements.view')->name('announcements.index');
        Route::get('/announcements/create',                             [AdminAnnouncement::class, 'create'])->middleware('permission:announcements.create')->name('announcements.create');
        Route::get('/announcements/print',                              [AdminAnnouncement::class, 'printView'])->name('announcements.print');
        Route::post('/announcements',                                   [AdminAnnouncement::class, 'store'])->middleware('permission:announcements.create')->name('announcements.store');
        Route::get('/announcements/{announcement}',                     [AdminAnnouncement::class, 'show'])->middleware('permission:announcements.view')->name('announcements.show');
        Route::get('/announcements/{announcement}/edit',                [AdminAnnouncement::class, 'edit'])->middleware('permission:announcements.edit')->name('announcements.edit');
        Route::match(['PUT','PATCH'], '/announcements/{announcement}', [AdminAnnouncement::class, 'update'])->middleware('permission:announcements.edit')->name('announcements.update');
        Route::delete('/announcements/{announcement}',                  [AdminAnnouncement::class, 'destroy'])->middleware('permission:announcements.delete')->name('announcements.destroy');
        Route::patch('/announcements/{announcement}/toggle',            [AdminAnnouncement::class, 'toggle'])->middleware('permission:announcements.publish')->name('announcements.toggle');
        Route::post('/announcements/bulk',                              [AdminAnnouncement::class, 'bulk'])->middleware('permission:announcements.publish')->name('announcements.bulk');

        // About Us
        Route::get('/about',                          [AdminAbout::class, 'index'])->middleware('permission:about.manage')->name('about.index');
        Route::post('/about/hero-image',              [AdminAbout::class, 'updateHeroImage'])->middleware('permission:about.manage')->name('about.hero-image.update');
        Route::post('/about/info',                    [AdminAbout::class, 'updateInfo'])->middleware('permission:about.manage')->name('about.updateInfo');
        Route::post('/about/leaders',                 [AdminAbout::class, 'storeLeader'])->middleware('permission:about.manage')->name('about.leaders.store');
        Route::post('/about/leaders/{leader}',        [AdminAbout::class, 'updateLeader'])->middleware('permission:about.manage')->name('about.leaders.update');
        Route::delete('/about/leaders/{leader}',      [AdminAbout::class, 'destroyLeader'])->middleware('permission:about.manage')->name('about.leaders.destroy');
        Route::post('/about/service-times',           [AdminAbout::class, 'updateServiceTimes'])->middleware('permission:about.manage')->name('about.service-times.update');
        Route::post('/about/pillars',                 [AdminAbout::class, 'storePillar'])->middleware('permission:about.manage')->name('about.pillars.store');
        Route::post('/about/pillars/{pillar}',        [AdminAbout::class, 'updatePillar'])->middleware('permission:about.manage')->name('about.pillars.update');
        Route::delete('/about/pillars/{pillar}',      [AdminAbout::class, 'destroyPillar'])->middleware('permission:about.manage')->name('about.pillars.destroy');

        // Correction Requests
        Route::get('/corrections',                   [AdminCorrection::class, 'index'])->middleware('permission:corrections.view')->name('corrections.index');
        Route::patch('/corrections/{correction}',    [AdminCorrection::class, 'update'])->middleware('permission:corrections.manage')->name('corrections.update');

        // Attendance
        Route::get('/attendance',                                           [AdminAttendance::class, 'index'])->middleware('permission:attendance.view')->name('attendance.index');
        Route::post('/attendance/open',                                     [AdminAttendance::class, 'open'])->middleware('permission:attendance.manage')->name('attendance.open');
        Route::post('/attendance/close/{session}',                         [AdminAttendance::class, 'close'])->middleware('permission:attendance.manage')->name('attendance.close');
        Route::get('/attendance/qr-codes',                                  [AdminAttendance::class, 'qrCodes'])->middleware('permission:attendance.view')->name('attendance.qr-codes');
        Route::get('/attendance/report',                                    [AdminAttendance::class, 'report'])->middleware('permission:attendance.view')->name('attendance.report');
        Route::get('/attendance/session/{session}',                        [AdminAttendance::class, 'session'])->middleware('permission:attendance.view')->name('attendance.session');
        Route::get('/attendance/session/{session}/search',                 [AdminAttendance::class, 'searchMember'])->middleware('permission:attendance.view')->name('attendance.session.search');
        Route::get('/attendance/session/{session}/count',                  fn(\App\Models\ServiceSession $session) => response()->json(['count' => $session->attendanceCount()]))->middleware('permission:attendance.view')->name('attendance.session.count');
        Route::post('/attendance/session/{session}/checkin',               [AdminAttendance::class, 'ushercheckin'])->middleware('permission:attendance.manage')->name('attendance.session.checkin');
        Route::delete('/attendance/checkin/{attendance}',                  [AdminAttendance::class, 'undoCheckin'])->middleware('permission:attendance.manage')->name('attendance.checkin.undo');
        Route::post('/attendance/followup',                                [AdminAttendance::class, 'saveFollowup'])->middleware('permission:attendance.view')->name('attendance.followup.save');
        Route::delete('/attendance/followup',                              [AdminAttendance::class, 'deleteFollowup'])->middleware('permission:attendance.view')->name('attendance.followup.delete');

        // My Profile (all admin users)
        Route::get('/my-profile',          [AdminMyProfile::class, 'edit'])->name('my-profile.edit');
        Route::patch('/my-profile',        [AdminMyProfile::class, 'update'])->name('my-profile.update');
        Route::patch('/my-profile/password',[AdminMyProfile::class, 'updatePassword'])->name('my-profile.password');

        // Settings (Super Admin only — isSuperAdmin() bypasses permission check in CheckPermission middleware)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::middleware('permission:settings.social')->group(function () {
                Route::get('/social',  [AdminSocial::class, 'index'])->name('social.index');
                Route::post('/social', [AdminSocial::class, 'update'])->name('social.update');
            });
            Route::middleware('permission:settings.users')->group(function () {
                Route::get('/users',                   [AdminSysUser::class, 'index'])->name('users.index');
                Route::get('/users/create',            [AdminSysUser::class, 'create'])->name('users.create');
                Route::post('/users',                  [AdminSysUser::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit',       [AdminSysUser::class, 'edit'])->name('users.edit');
                Route::match(['PUT','PATCH'], '/users/{user}', [AdminSysUser::class, 'update'])->name('users.update');
                Route::delete('/users/{user}',         [AdminSysUser::class, 'destroy'])->name('users.destroy');
                Route::patch('/users/{user}/toggle',   [AdminSysUser::class, 'toggleActive'])->name('users.toggle');
                Route::patch('/users/{user}/password', [AdminSysUser::class, 'resetPassword'])->name('users.password');
            });
            Route::middleware('permission:settings.roles')->group(function () {
                Route::get('/roles',                        [AdminRolePerms::class, 'index'])->name('roles.index');
                Route::post('/roles',                       [AdminRolePerms::class, 'storeRole'])->name('roles.store');
                Route::put('/roles/{role}',                 [AdminRolePerms::class, 'updateRole'])->name('roles.update');
                Route::delete('/roles/{role}',              [AdminRolePerms::class, 'destroyRole'])->name('roles.destroy');
                Route::post('/permissions',                 [AdminRolePerms::class, 'storePermission'])->name('permissions.store');
                Route::delete('/permissions/{permission}',  [AdminRolePerms::class, 'destroyPermission'])->name('permissions.destroy');
            });
            Route::middleware('permission:settings.logs')->group(function () {
                Route::get('/logs', [AdminSysLog::class, 'index'])->name('logs.index');
            });
            Route::middleware('permission:settings.social')->group(function () {
                Route::get('/verify-access',  [AdminVerifyAccess::class, 'index'])->name('verify-access.index');
                Route::post('/verify-access', [AdminVerifyAccess::class, 'update'])->name('verify-access.update');
            });
        });

        // Members
        Route::get('/members/print',          [AdminMember::class, 'printList'])->middleware('permission:members.view')->name('members.print');
        Route::get('/members/import',         [MemberImportController::class, 'showForm'])->middleware('permission:members.import')->name('members.import');
        Route::post('/members/import',        [MemberImportController::class, 'import'])->middleware('permission:members.import')->name('members.import.store');
        Route::get('/members',                [AdminMember::class, 'index'])->middleware('permission:members.view')->name('members.index');
        Route::get('/members/report',         [AdminMember::class, 'report'])->middleware('permission:members.view')->name('members.report');
        Route::get('/members/create',         [AdminMember::class, 'create'])->middleware('permission:members.create')->name('members.create');
        Route::post('/members',               [AdminMember::class, 'store'])->middleware('permission:members.create')->name('members.store');
        Route::get('/members/{user}',         [AdminMember::class, 'show'])->middleware('permission:members.view')->name('members.show');
        Route::get('/members/{user}/edit',    [AdminMember::class, 'edit'])->middleware('permission:members.edit')->name('members.edit');
        Route::match(['PUT','PATCH'], '/members/{user}', [AdminMember::class, 'update'])->middleware('permission:members.edit')->name('members.update');
        Route::patch('/members/{user}/role',  [AdminMember::class, 'updateRole'])->middleware('permission:members.edit')->name('members.updateRole');

        }); // end force.password.change middleware group
    });

require __DIR__.'/auth.php';
