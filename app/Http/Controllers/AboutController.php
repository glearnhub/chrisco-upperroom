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

        $founder  = ChurchLeader::active()->where('role', 'founder')->first();
        $bishops  = ChurchLeader::active()->where('role', 'bishop')->get();
        $pastors  = ChurchLeader::active()->where('role', 'pastor')->get();
        $others   = ChurchLeader::active()->where('role', 'other')->get();

        $pillars  = ChurchPillar::active()->get();

        return view('about.index', compact(
            'whoWeAre', 'vision', 'mission',
            'founder', 'bishops', 'pastors', 'others', 'pillars'
        ));
    }
}
