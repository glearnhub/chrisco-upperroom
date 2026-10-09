<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ChurchSetting;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'service_1_name'       => 'Sunday Service',
            'service_1_subtitle'   => 'Main Worship Service',
            'service_1_time'       => '9:00 AM',
            'service_1_icon'       => 'fas fa-sun',
            'service_1_color'      => '#c0392b',

            'service_2_name'       => 'Wednesday Revival Kesha',
            'service_2_subtitle'   => 'Interdenominational Kesha',
            'service_2_time'       => '8:00 PM – 5:00 AM',
            'service_2_icon'       => 'fas fa-book-open',
            'service_2_color'      => '#0a1f44',

            'service_3_name'       => 'Thursday Revival Service',
            'service_3_subtitle'   => 'Interdenominational Service',
            'service_3_time'       => '5:00 PM – 8:00 PM',
            'service_3_icon'       => 'fas fa-praying-hands',
            'service_3_color'      => '#f0a500',
        ];

        foreach ($defaults as $key => $value) {
            ChurchSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public function down(): void
    {
        \DB::table('church_settings')->whereIn('key', [
            'service_1_name','service_1_subtitle','service_1_time','service_1_icon','service_1_color',
            'service_2_name','service_2_subtitle','service_2_time','service_2_icon','service_2_color',
            'service_3_name','service_3_subtitle','service_3_time','service_3_icon','service_3_color',
        ])->delete();
    }
};
