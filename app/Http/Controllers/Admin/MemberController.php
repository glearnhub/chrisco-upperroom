<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('department')) {
            $dept = $request->department;
            $query->where(function ($q) use ($dept) {
                $q->where('department',  $dept)
                  ->orWhere('department2', $dept)
                  ->orWhere('department3', $dept);
            });
        }
        if ($request->filled('deacon')) {
            $query->where('deacon_name', $request->deacon);
        }
        if ($request->filled('home_cell')) {
            $query->where('home_cell', $request->home_cell);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // CSV export
        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        $members = $query->paginate(20)->withQueryString();

        // Filter options for dropdowns
        $deacons    = User::whereNotNull('deacon_name')->pluck('deacon_name')->unique()->sort()->values();
        $d1 = User::whereNotNull('department')->where('department','!=','')->pluck('department');
        $d2 = User::whereNotNull('department2')->where('department2','!=','')->pluck('department2');
        $d3 = User::whereNotNull('department3')->where('department3','!=','')->pluck('department3');
        $departments = $d1->merge($d2)->merge($d3)->unique()->sort()->values();
        $homeCells  = User::whereNotNull('home_cell')->pluck('home_cell')->unique()->sort()->values();

        return view('admin.members.index', compact('members', 'deacons', 'departments', 'homeCells'));
    }

    public function printList(Request $request)
    {
        $query = User::where('role', 'member');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn ($s) => $s->where('name', 'like', "%$q%")
                ->orWhere('last_name', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%"));
        }
        if ($request->filled('gender'))     $query->where('gender', $request->gender);
        if ($request->filled('department')) $query->where('department', $request->department);

        $members = $query->orderBy('name')->get();

        return view('admin.members.print', compact('members'));
    }

    public function report(Request $request)
    {
        $deacons = User::whereNotNull('deacon_name')->pluck('deacon_name')->unique()->sort()->values();
        $selectedDeacon = $request->deacon;

        $query = User::where('role', 'member');
        if ($selectedDeacon) {
            $query->where('deacon_name', $selectedDeacon);
        }
        $members = $query->orderBy('name')->get();

        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportCsv($members, 'deacon_report');
        }

        return view('admin.members.report', compact('deacons', 'selectedDeacon', 'members'));
    }

    private function exportCsv($members, $type = 'members'): StreamedResponse
    {
        $filename = $type . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($members) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'First Name', 'Middle Name', 'Last Name', 'Gender', 'Marital Status',
                'Email', 'Phone', 'Date of Birth', 'Membership Date',
                'County', 'Sub County', 'Sub Location/Estate',
                'Month/Year of Salvation', 'Committed Member', 'Month/Year Committed',
                'Department 1', 'Department 2', 'Department 3', 'Occupation',
                'Home Cell', 'Deacon/Deaconess',
                'Next of Kin', 'Relationship', 'Next of Kin Phone',
                'Office', 'Role', 'Joined System',
            ]);

            foreach ($members as $m) {
                fputcsv($handle, [
                    $m->name,
                    $m->middle_name ?? '',
                    $m->last_name ?? '',
                    $m->gender ? ucfirst($m->gender) : '',
                    $m->marital_status ? ucfirst($m->marital_status) : '',
                    $m->email,
                    $m->phone ?? '',
                    $m->date_of_birth ? $m->date_of_birth->format('d/m/Y') : '',
                    $m->membership_date ? $m->membership_date->format('d/m/Y') : '',
                    $m->county ?? '',
                    $m->sub_county ?? '',
                    $m->sub_location ?? '',
                    $m->salvation_date ?? '',
                    $m->is_committed_member ? 'Yes' : 'No',
                    $m->committed_date ?? '',
                    $m->department ?? '',
                    $m->department2 ?? '',
                    $m->department3 ?? '',
                    $m->occupation ?? '',
                    $m->home_cell ?? '',
                    $m->deacon_name ?? '',
                    $m->next_of_kin_name ?? '',
                    $m->next_of_kin_relationship ?? '',
                    $m->next_of_kin_phone ?? '',
                    $m->office ? ucfirst($m->office) : '',
                    $m->role === 'admin' ? 'IT Support' : 'Member',
                    $m->created_at->format('d/m/Y'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'middle_name'             => 'nullable|string|max:255',
            'last_name'               => 'nullable|string|max:255',
            'gender'                  => 'nullable|in:male,female',
            'marital_status'          => 'nullable|in:single,married,widowed,divorced,separated',
            'email'                   => 'required|email|unique:users,email',
            'phone'                   => 'nullable|string|max:20',
            'date_of_birth'           => 'nullable|date',
            'membership_date'         => 'nullable|date',
            'role'                    => 'nullable|in:member',
            'password'                => 'required|string|min:8|confirmed',
            'county'                  => 'nullable|string|max:100',
            'sub_county'              => 'nullable|string|max:100',
            'sub_location'            => 'nullable|string|max:255',
            'salvation_date'          => 'nullable|string|max:50',
            'is_committed_member'     => 'boolean',
            'committed_date'          => 'nullable|string|max:50',
            'department'              => 'nullable|string|max:100',
            'department2'             => 'nullable|string|max:100',
            'department3'             => 'nullable|string|max:100',
            'occupation'              => 'nullable|string|max:255',
            'next_of_kin_name'        => 'nullable|string|max:255',
            'next_of_kin_relationship'=> 'nullable|string|max:100',
            'next_of_kin_phone'       => 'nullable|string|max:20',
            'belongs_to_home_cell'    => 'boolean',
            'home_cell'               => 'nullable|string|max:255',
            'assigned_to_deacon'      => 'boolean',
            'deacon_name'             => 'nullable|string|max:255',
            'office'                  => 'nullable|in:presbyter,pastor,elder,deacon,deaconess',
        ]);

        $validated['belongs_to_home_cell'] = $request->boolean('belongs_to_home_cell');
        $validated['assigned_to_deacon']   = $request->boolean('assigned_to_deacon');
        $validated['is_committed_member']  = $request->boolean('is_committed_member');
        $validated['password']             = Hash::make($validated['password']);
        $validated['membership_date']      = $validated['membership_date'] ?? now()->toDateString();
        $validated['role']                 = 'member'; // always member — admins created via Settings > System Users

        User::create($validated);

        return redirect()->route('admin.members.index')
            ->with('success', "Member {$validated['name']} added successfully.");
    }

    public function show(User $user)
    {
        $member = $user;
        $donations = $member->donations()->latest()->get();
        $eventRegistrations = $member->eventRegistrations()->with('event')->latest()->get();
        return view('admin.members.show', compact('member', 'donations', 'eventRegistrations'));
    }

    public function edit(User $user)
    {
        return view('admin.members.edit', ['member' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'middle_name'             => 'nullable|string|max:255',
            'last_name'               => 'nullable|string|max:255',
            'gender'                  => 'nullable|in:male,female',
            'marital_status'          => 'nullable|in:single,married,widowed,divorced,separated',
            'email'                   => 'required|email|unique:users,email,' . $user->id,
            'phone'                   => 'nullable|string|max:20',
            'date_of_birth'           => 'nullable|date',
            'membership_date'         => 'nullable|date',
            'county'                  => 'nullable|string|max:100',
            'sub_county'              => 'nullable|string|max:100',
            'sub_location'            => 'nullable|string|max:255',
            'salvation_date'          => 'nullable|string|max:50',
            'is_committed_member'     => 'boolean',
            'committed_date'          => 'nullable|string|max:50',
            'department'              => 'nullable|string|max:100',
            'department2'             => 'nullable|string|max:100',
            'department3'             => 'nullable|string|max:100',
            'occupation'              => 'nullable|string|max:255',
            'next_of_kin_name'        => 'nullable|string|max:255',
            'next_of_kin_relationship'=> 'nullable|string|max:100',
            'next_of_kin_phone'       => 'nullable|string|max:20',
            'home_cell'               => 'nullable|string|max:255',
            'deacon_name'             => 'nullable|string|max:255',
            'office'                  => 'nullable|in:presbyter,pastor,elder,deacon,deaconess',
        ]);

        $validated['is_committed_member'] = $request->boolean('is_committed_member');
        $validated['belongs_to_home_cell'] = $request->boolean('belongs_to_home_cell');
        $validated['assigned_to_deacon']   = $request->boolean('assigned_to_deacon');

        $user->update($validated);

        return redirect()->route('admin.members.show', $user)
            ->with('success', 'Member profile updated.');
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate(['role' => 'required|in:admin,member']);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $validated['role']]);
        return back()->with('success', "Role updated for {$user->name}.");
    }
}
