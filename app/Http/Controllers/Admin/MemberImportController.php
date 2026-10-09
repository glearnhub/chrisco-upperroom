<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MemberImportController extends Controller
{
    public function showForm()
    {
        return view('admin.members.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $path = $request->file('file')->getRealPath();
        $ext  = strtolower($request->file('file')->getClientOriginalExtension());

        try {
            if ($ext === 'csv') {
                $rows = $this->readCsv($path);
            } else {
                $rows = $this->readExcel($path);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Could not read file: ' . $e->getMessage());
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $lineNum => $row) {
            // Skip completely empty rows
            $values = array_filter(array_values($row), fn($v) => $v !== null && $v !== '');
            if (empty($values)) continue;

            $email = $this->val($row, [
                'Email', 'email', 'EMAIL',
                'Email Address', 'email address',
            ]);

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$lineNum}: Invalid or missing email — skipped.";
                $skipped++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Row {$lineNum}: {$email} already exists — skipped.";
                $skipped++;
                continue;
            }

            $homeCellAnswer = strtolower($this->val($row, ['Do you belong to a Home Cell?']) ?? '');
            $deaconAnswer   = strtolower($this->val($row, ['Are you assigned to any Deacon or Deaconess?']) ?? '');

            $dobRaw = $this->val($row, ['DOB', 'Date of Birth', 'dob']);
            $dob    = null;
            if ($dobRaw) {
                try {
                    if ($dobRaw instanceof \DateTime) {
                        $dob = $dobRaw->format('Y-m-d');
                    } elseif (is_numeric($dobRaw)) {
                        $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dobRaw)->format('Y-m-d');
                    } else {
                        $dob = date('Y-m-d', strtotime($dobRaw));
                    }
                } catch (\Exception) {}
            }

            $membershipRaw = $this->val($row, ['Date of Joining CUR', 'Membership Date', 'Date Joined']);
            $membershipDate = null;
            if ($membershipRaw) {
                try {
                    if ($membershipRaw instanceof \DateTime) {
                        $membershipDate = $membershipRaw->format('Y-m-d');
                    } elseif (is_numeric($membershipRaw)) {
                        $membershipDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($membershipRaw)->format('Y-m-d');
                    } else {
                        $membershipDate = date('Y-m-d', strtotime($membershipRaw));
                    }
                } catch (\Exception) {}
            }

            User::create([
                'name'                    => $this->val($row, ['First Name', 'first name', 'firstname', 'First_Name']) ?? 'Unknown',
                'middle_name'             => $this->val($row, ['Middle Name', 'middle name']),
                'last_name'               => $this->val($row, ['Last Name', 'last name', 'lastname', 'Surname']),
                'gender'                  => $this->mapGender($this->val($row, ['Gender', 'gender'])),
                'email'                   => $email,
                'phone'                   => $this->val($row, ['Phone Number', 'Phone', 'phone', 'Mobile']),
                'date_of_birth'           => $dob,
                'county'                  => $this->val($row, ['County of Residence', 'County', 'county']),
                'sub_county'              => $this->val($row, ['Sub County', 'sub county', 'sub_county']),
                'sub_location'            => $this->val($row, ['Sub Location/Estate', 'Sub Location', 'Estate']),
                'salvation_date'          => $this->val($row, ['Month and year when of Salvation', 'Salvation Date', 'Month/Year of Salvation']),
                'membership_date'         => $membershipDate,
                'committed_date'          => $this->val($row, ['Month and year Committed', 'Committed Date']),
                'department'              => $this->val($row, ['Department', 'department']),
                'occupation'              => $this->val($row, ['Career/Occupation', 'Occupation', 'Career', 'occupation']),
                'next_of_kin_name'        => $this->val($row, ['Next of Kin that can be reached in case of an emergency', 'Next of Kin', 'Next of Kin Name']),
                'next_of_kin_relationship'=> $this->val($row, ['Relationship with the next of Kin', 'Next of Kin Relationship', 'Relationship']),
                'next_of_kin_phone'       => $this->val($row, ['Phone Number of the Next of Kin', 'Next of Kin Phone']),
                'belongs_to_home_cell'    => in_array($homeCellAnswer, ['yes', 'y', '1', 'true']),
                'home_cell'               => $this->val($row, ['If Yes, which one?', 'Home Cell', 'home cell']),
                'assigned_to_deacon'      => in_array($deaconAnswer, ['yes', 'y', '1', 'true']),
                'deacon_name'             => $this->val($row, ['If yes, mention their name', 'Deacon Name', 'Deaconess Name', 'Deacon/Deaconess']),
                'role'                    => 'member',
                'password'                => Hash::make(Str::random(32)),
                'must_change_password'    => true,
            ]);

            $imported++;
        }

        $msg = "Import complete: {$imported} members imported, {$skipped} skipped.";
        return redirect()->route('admin.members.index')
            ->with('success', $msg)
            ->with('import_errors', $errors);
    }

    private function readExcel(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $data  = $sheet->toArray(null, true, true, false);

        if (empty($data)) return [];

        $headers = array_map('trim', array_shift($data));
        $rows = [];
        foreach ($data as $i => $row) {
            $rows[$i + 2] = array_combine($headers, array_pad($row, count($headers), null));
        }
        return $rows;
    }

    private function readCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');
        $headers = array_map('trim', fgetcsv($handle));
        $i = 2;
        while (($row = fgetcsv($handle)) !== false) {
            $rows[$i++] = array_combine($headers, array_pad($row, count($headers), null));
        }
        fclose($handle);
        return $rows;
    }

    private function val(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
                return trim((string) $row[$key]);
            }
        }
        return null;
    }

    private function mapGender(?string $val): ?string
    {
        if (!$val) return null;
        $v = strtolower(trim($val));
        if (in_array($v, ['male', 'm'])) return 'male';
        if (in_array($v, ['female', 'f'])) return 'female';
        return null;
    }
}
