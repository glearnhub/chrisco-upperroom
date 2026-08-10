<?php

namespace App\Http\Controllers;

use App\Models\ChurchLeader;
use App\Models\ChurchPillar;
use App\Models\ChurchSetting;

class AboutController extends Controller
{
    public function index()
    {
        $whoWeAre = ChurchSetting::get('who_we_are');
        $vision   = ChurchSetting::get('vision');
        $mission  = ChurchSetting::get('mission');

        $leaders = ChurchLeader::active()->get();

        $pillars = ChurchPillar::active()->get();

        return view('about.index', compact(
            'whoWeAre', 'vision', 'mission',
            'leaders', 'pillars'
        ));
    }
}
