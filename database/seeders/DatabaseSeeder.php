<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sermon;
use App\Models\Event;
use App\Models\Livestream;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // Admin User
        // -------------------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@chrisco.church'],
            [
                'name'            => 'Pastor Admin',
                'email'           => 'admin@chrisco.church',
                'password'        => Hash::make('Admin@1234'),
                'role'            => 'admin',
                'phone'           => '0726900700',
                'address'         => 'Nairobi, Kenya',
                'membership_date' => now(),
            ]
        );

        // -------------------------------------------------------
        // Sample Sermons
        // -------------------------------------------------------
        Sermon::updateOrCreate(
            ['title' => 'Walking in the Spirit'],
            [
                'title'       => 'Walking in the Spirit',
                'speaker'     => 'Pastor Admin',
                'description' => 'A powerful message on living a Spirit-led life day by day. We explore what it means to yield to the Holy Spirit in every area of our lives.',
                'sermon_date' => now()->subDays(7),
                'series'      => 'Life in the Spirit',
                'scripture'   => 'Galatians 5:16-25',
                'video_url'   => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'status'      => 'published',
                'views'       => 0,
            ]
        );

        Sermon::updateOrCreate(
            ['title' => 'The Power of Prayer'],
            [
                'title'       => 'The Power of Prayer',
                'speaker'     => 'Pastor Admin',
                'description' => 'Discover the transforming power of a consistent prayer life. This sermon challenges us to prioritize prayer and trust God for the impossible.',
                'sermon_date' => now()->subDays(14),
                'series'      => 'Prayer and Faith',
                'scripture'   => 'Matthew 6:5-15',
                'video_url'   => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'status'      => 'published',
                'views'       => 0,
            ]
        );

        // -------------------------------------------------------
        // Sample Events
        // -------------------------------------------------------
        Event::updateOrCreate(
            ['title' => 'Sunday Worship Service'],
            [
                'title'                 => 'Sunday Worship Service',
                'description'           => 'Join us every Sunday for a powerful time of worship, prayer, and the Word of God. All are welcome — come as you are!',
                'location'              => 'Chrisco Upperroom Fellowship, Nairobi, Kenya',
                'start_datetime'        => now()->next('Sunday')->setTime(10, 0),
                'end_datetime'          => now()->next('Sunday')->setTime(13, 0),
                'capacity'              => null,
                'registration_required' => false,
                'status'                => 'upcoming',
            ]
        );

        Event::updateOrCreate(
            ['title' => 'Youth Conference 2025'],
            [
                'title'                 => 'Youth Conference 2025',
                'description'           => 'An electrifying two-day conference for the youth of Chrisco Upperroom. Expect powerful sessions, worship, and fellowship.',
                'location'              => 'Chrisco Upperroom Fellowship Hall, Nairobi',
                'start_datetime'        => now()->addDays(30)->setTime(8, 0),
                'end_datetime'          => now()->addDays(31)->setTime(18, 0),
                'capacity'              => 200,
                'registration_required' => true,
                'status'                => 'upcoming',
            ]
        );

        // -------------------------------------------------------
        // Livestream
        // -------------------------------------------------------
        Livestream::updateOrCreate(
            ['title' => 'Sunday Live Service'],
            [
                'title'        => 'Sunday Live Service',
                'description'  => 'Watch our Sunday worship service live. Join us from wherever you are!',
                'embed_url'    => 'https://www.youtube.com/embed/live_stream?channel=UCxxxxxx',
                'platform'     => 'YouTube',
                'is_live'      => false,
                'scheduled_at' => now()->next('Sunday')->setTime(10, 0),
            ]
        );

        // -------------------------------------------------------
        // Announcements
        // -------------------------------------------------------
        Announcement::updateOrCreate(
            ['title' => 'Welcome to Chrisco Upperroom Fellowship'],
            [
                'title'        => 'Welcome to Chrisco Upperroom Fellowship',
                'body'         => 'We warmly welcome you to Chrisco Upperroom Fellowship — Where God Dwells. Whether you are joining us for the first time or are a long-standing member, we are glad you are here. Our doors are always open.',
                'is_published' => true,
                'expires_at'   => null,
            ]
        );

        Announcement::updateOrCreate(
            ['title' => 'Youth Conference Registration Now Open'],
            [
                'title'        => 'Youth Conference Registration Now Open',
                'body'         => 'Registration for the Youth Conference 2025 is now open! Slots are limited to 200 participants. Register early to secure your spot. The conference runs for two days and will feature dynamic speakers, worship and networking.',
                'is_published' => true,
                'expires_at'   => now()->addDays(25),
            ]
        );
    }
}
