<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchLeader;
use App\Models\ChurchPillar;
use App\Models\ChurchSetting;
use App\Models\User;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $leaders = ChurchLeader::orderBy('sort_order')->orderBy('created_at')->get();
        $pillars = ChurchPillar::orderBy('sort_order')->orderBy('created_at')->get();

        $members = User::whereIn('office', ['presbyter','pastor','elder','deacon','deaconess'])
            ->orderByRaw("CASE office WHEN 'presbyter' THEN 1 WHEN 'pastor' THEN 2 WHEN 'elder' THEN 3 WHEN 'deacon' THEN 4 WHEN 'deaconess' THEN 5 ELSE 6 END")
            ->orderBy('name')
            ->get(['id','name','middle_name','last_name','office','profile_photo']);

        return view('admin.about.index', compact('leaders', 'pillars', 'members'));
    }

    // ── Church Info (Who We Are / Vision / Mission) ──────────────────────────

    public function updateInfo(Request $request)
    {
        $validated = $request->validate([
            'who_we_are' => 'nullable|string',
            'vision'     => 'nullable|string|max:1000',
            'mission'    => 'nullable|string|max:1000',
        ]);

        ChurchSetting::set('who_we_are', $validated['who_we_are'] ?? null);
        ChurchSetting::set('vision',     $validated['vision']     ?? null);
        ChurchSetting::set('mission',    $validated['mission']    ?? null);

        return back()->with('success', 'Church information updated successfully.');
    }

    // ── Leaders ───────────────────────────────────────────────────────────────

    public function storeLeader(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'title'      => 'required|string|max:255',
            'role'       => 'required|string|max:150',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('leaders', 'public');
        }

        ChurchLeader::create([
            'name'       => $validated['name'],
            'title'      => $validated['title'],
            'role'       => $validated['role'],
            'bio'        => $validated['bio'] ?? null,
            'photo'      => $path,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Leader added successfully.');
    }

    public function updateLeader(Request $request, ChurchLeader $leader)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'title'      => 'required|string|max:255',
            'role'       => 'required|string|max:150',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('leaders', 'public');
        } else {
            unset($validated['photo']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $leader->update($validated);

        return back()->with('success', 'Leader updated successfully.');
    }

    public function destroyLeader(ChurchLeader $leader)
    {
        $leader->delete();
        return back()->with('success', 'Leader removed.');
    }

    // ── Pillars ───────────────────────────────────────────────────────────────

    public function storePillar(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'icon'        => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        ChurchPillar::create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'icon'        => $validated['icon'] ?? 'fas fa-cross',
            'sort_order'  => $validated['sort_order'] ?? 0,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Core pillar added successfully.');
    }

    public function updatePillar(Request $request, ChurchPillar $pillar)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'icon'        => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $pillar->update($validated);

        return back()->with('success', 'Core pillar updated.');
    }

    public function destroyPillar(ChurchPillar $pillar)
    {
        $pillar->delete();
        return back()->with('success', 'Core pillar removed.');
    }
}
