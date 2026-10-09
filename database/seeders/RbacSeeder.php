<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ────────────────────────────────────────────────────

        $perms = [
            // Dashboard
            ['module' => 'Dashboard',      'slug' => 'dashboard.view',             'name' => 'View Dashboard'],

            // Members
            ['module' => 'Members',        'slug' => 'members.view',               'name' => 'View Members'],
            ['module' => 'Members',        'slug' => 'members.create',             'name' => 'Create Members'],
            ['module' => 'Members',        'slug' => 'members.edit',               'name' => 'Edit Members'],
            ['module' => 'Members',        'slug' => 'members.delete',             'name' => 'Delete Members'],
            ['module' => 'Members',        'slug' => 'members.import',             'name' => 'Import Members'],

            // Children
            ['module' => 'Children',       'slug' => 'children.view',              'name' => 'View Children'],
            ['module' => 'Children',       'slug' => 'children.create',            'name' => 'Add Children'],
            ['module' => 'Children',       'slug' => 'children.edit',              'name' => 'Edit Children'],
            ['module' => 'Children',       'slug' => 'children.delete',            'name' => 'Delete Children'],

            // Calendar
            ['module' => 'Calendar',       'slug' => 'calendar.manage',            'name' => 'Manage Church Calendar'],

            // Attendance
            ['module' => 'Attendance',     'slug' => 'attendance.view',            'name' => 'View Attendance'],
            ['module' => 'Attendance',     'slug' => 'attendance.manage',          'name' => 'Manage Attendance Sessions'],
            ['module' => 'Attendance',     'slug' => 'attendance.followup',        'name' => 'Record Attendance Follow-Up'],

            // Sermons
            ['module' => 'Sermons',        'slug' => 'sermons.view',               'name' => 'View Sermons'],
            ['module' => 'Sermons',        'slug' => 'sermons.create',             'name' => 'Upload Sermons'],
            ['module' => 'Sermons',        'slug' => 'sermons.edit',               'name' => 'Edit Sermons'],
            ['module' => 'Sermons',        'slug' => 'sermons.delete',             'name' => 'Delete Sermons'],

            // Teachings
            ['module' => 'Teachings',      'slug' => 'teachings.view',             'name' => 'View Teachings'],
            ['module' => 'Teachings',      'slug' => 'teachings.create',           'name' => 'Upload Teachings'],
            ['module' => 'Teachings',      'slug' => 'teachings.edit',             'name' => 'Edit Teachings'],
            ['module' => 'Teachings',      'slug' => 'teachings.delete',           'name' => 'Delete Teachings'],

            // Resources
            ['module' => 'Resources',      'slug' => 'resources.view',             'name' => 'View Resources'],
            ['module' => 'Resources',      'slug' => 'resources.create',           'name' => 'Upload Resources'],
            ['module' => 'Resources',      'slug' => 'resources.edit',             'name' => 'Edit Resources'],
            ['module' => 'Resources',      'slug' => 'resources.delete',           'name' => 'Delete Resources'],

            // Gallery
            ['module' => 'Gallery',        'slug' => 'gallery.view',               'name' => 'View Gallery'],
            ['module' => 'Gallery',        'slug' => 'gallery.create',             'name' => 'Upload Gallery Images'],
            ['module' => 'Gallery',        'slug' => 'gallery.edit',               'name' => 'Edit Gallery Images'],
            ['module' => 'Gallery',        'slug' => 'gallery.delete',             'name' => 'Delete Gallery Images'],

            // Livestream
            ['module' => 'Livestream',     'slug' => 'livestream.view',            'name' => 'View Livestream'],
            ['module' => 'Livestream',     'slug' => 'livestream.manage',          'name' => 'Manage Livestream'],

            // Events
            ['module' => 'Events',         'slug' => 'events.view',                'name' => 'View Events'],
            ['module' => 'Events',         'slug' => 'events.create',              'name' => 'Create Events'],
            ['module' => 'Events',         'slug' => 'events.edit',                'name' => 'Edit Events'],
            ['module' => 'Events',         'slug' => 'events.delete',              'name' => 'Delete Events'],
            ['module' => 'Events',         'slug' => 'events.reports',             'name' => 'View Event Reports'],

            // Announcements
            ['module' => 'Announcements',  'slug' => 'announcements.view',         'name' => 'View Announcements'],
            ['module' => 'Announcements',  'slug' => 'announcements.create',       'name' => 'Create Announcements'],
            ['module' => 'Announcements',  'slug' => 'announcements.edit',         'name' => 'Edit Announcements'],
            ['module' => 'Announcements',  'slug' => 'announcements.delete',       'name' => 'Delete Announcements'],
            ['module' => 'Announcements',  'slug' => 'announcements.publish',      'name' => 'Publish Announcements'],

            // Prayers
            ['module' => 'Prayers',        'slug' => 'prayers.view',               'name' => 'View Prayer Requests'],
            ['module' => 'Prayers',        'slug' => 'prayers.manage',             'name' => 'Manage Prayer Requests'],

            // Givings / Donations
            ['module' => 'Givings',        'slug' => 'givings.view',               'name' => 'View Givings'],
            ['module' => 'Givings',        'slug' => 'givings.manage',             'name' => 'Manage Givings'],

            // Reports
            ['module' => 'Reports',        'slug' => 'reports.membership',         'name' => 'View Membership Reports'],
            ['module' => 'Reports',        'slug' => 'reports.children',           'name' => 'View Children Reports'],
            ['module' => 'Reports',        'slug' => 'reports.events',             'name' => 'View Event Reports'],

            // Settings
            ['module' => 'Settings',       'slug' => 'settings.social',            'name' => 'Manage Social Media Links'],
            ['module' => 'Settings',       'slug' => 'settings.users',             'name' => 'Manage System Users'],
            ['module' => 'Settings',       'slug' => 'settings.roles',             'name' => 'Manage Roles & Permissions'],
            ['module' => 'Settings',       'slug' => 'settings.logs',              'name' => 'View System Logs'],

            // Visitors
            ['module' => 'Visitors',       'slug' => 'visitors.view',              'name' => 'View Visitors'],
            ['module' => 'Visitors',       'slug' => 'visitors.create',            'name' => 'Add Visitors'],
            ['module' => 'Visitors',       'slug' => 'visitors.edit',              'name' => 'Edit Visitors'],
            ['module' => 'Visitors',       'slug' => 'visitors.delete',            'name' => 'Delete Visitors'],

            // Correction Requests
            ['module' => 'Corrections',    'slug' => 'corrections.view',           'name' => 'View Correction Requests'],
            ['module' => 'Corrections',    'slug' => 'corrections.manage',         'name' => 'Manage Correction Requests'],

            // About
            ['module' => 'About',          'slug' => 'about.manage',               'name' => 'Manage About Us'],
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], array_merge($p, ['description' => $p['name']]));
        }

        // ── Roles ──────────────────────────────────────────────────────────

        $superAdmin = Role::firstOrCreate(['slug' => 'super_admin'], [
            'name' => 'Super Admin', 'description' => 'Unrestricted access to everything', 'is_super_admin' => true,
        ]);

        $media = Role::firstOrCreate(['slug' => 'cur_media'], [
            'name' => 'Church Media Administrator', 'description' => 'Manages livestream and gallery',
        ]);
        $media->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', ['livestream.view','livestream.manage','gallery.view','gallery.create','gallery.edit','gallery.delete'])->pluck('id')
        );

        $school = Role::firstOrCreate(['slug' => 'cur_school'], [
            'name' => 'Sunday School Administrator', 'description' => 'Manages children records',
        ]);
        $school->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', ['children.view','children.create','children.edit','children.delete','reports.children'])->pluck('id')
        );

        $events = Role::firstOrCreate(['slug' => 'cur_events'], [
            'name' => 'Events Administrator', 'description' => 'Manages events and registrations',
        ]);
        $events->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', ['events.view','events.create','events.edit','events.delete','events.reports','reports.events','calendar.manage'])->pluck('id')
        );

        $announcements = Role::firstOrCreate(['slug' => 'cur_announcements'], [
            'name' => 'Announcements Administrator', 'description' => 'Manages announcements',
        ]);
        $announcements->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', ['announcements.view','announcements.create','announcements.edit','announcements.delete','announcements.publish'])->pluck('id')
        );

        $membership = Role::firstOrCreate(['slug' => 'cur_admin'], [
            'name' => 'Membership Administrator', 'description' => 'Manages members and membership data',
        ]);
        $membership->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', ['members.view','members.create','members.edit','members.delete','members.import','visitors.view','visitors.create','visitors.edit','visitors.delete','corrections.view','corrections.manage','reports.membership','attendance.view','attendance.manage','attendance.followup','calendar.manage'])->pluck('id')
        );

        $visitors = Role::firstOrCreate(['slug' => 'cur_visitors'], [
            'name' => 'Visitors Administrator', 'description' => 'Manages visitor records and follow-ups',
        ]);
        $visitors->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', ['visitors.view','visitors.create','visitors.edit','visitors.delete'])->pluck('id')
        );

        $prayers = Role::firstOrCreate(['slug' => 'cur_prayers'], [
            'name' => 'Prayer Request Administrator', 'description' => 'Views and manages prayer requests assigned to them',
        ]);
        $prayers->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', ['prayers.view','prayers.manage'])->pluck('id')
        );

        // ── Default Super Admin User ────────────────────────────────────────

        $superUser = User::where('email', 'superadmin@chrisco-upper-room.org')->first();
        if (!$superUser) {
            $superUser = User::create([
                'name'      => 'Super',
                'last_name' => 'Admin',
                'email'     => 'superadmin@chrisco-upper-room.org',
                'password'  => Hash::make(env('SUPER_ADMIN_PASSWORD', 'SuperAdmin@2024!')),
                'role'      => 'admin',
                'is_active' => true,
            ]);
        }

        $superUser->roles()->syncWithoutDetaching([$superAdmin->id]);

        $this->command->info('✓ RBAC seeded successfully.');
        $this->command->info('  Super Admin: superadmin@chrisco-upper-room.org (password from SUPER_ADMIN_PASSWORD in .env)');
    }
}
