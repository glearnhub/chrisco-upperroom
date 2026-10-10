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
        $mime = $request->file('file')->getMimeType();

        try {
            if (in_array($mime, ['text/csv', 'text/plain'])) {
                $rows = $this->readCsv($path);
            } else {
                $rows = $this->readExcel($path);
            }
        } catch (\Exception $e) {
            \Log::error('Member import failed: ' . $e->getMessage());
            return back()->with('error', 'Could not read the uploaded file. Please ensure it is a valid Excel or CSV file.');
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

            $homeCellAnswer   = strtolower($this->val($row, ['Do you belong to a Home Cell?', 'Belongs to Home Cell', 'Home Cell?']) ?? '');
            $deaconAnswer     = strtolower($this->val($row, ['Are you assigned to any Deacon or Deaconess?', 'Assigned to Deacon']) ?? '');
            $bornAgainAnswer  = strtolower($this->val($row, ['Born Again', 'Is Born Again', 'Are you Born Again?']) ?? '');
            $baptizedAnswer   = strtolower($this->val($row, ['Baptized', 'Is Baptized', 'Are you Baptized?', 'Baptised']) ?? '');
            $committedAnswer  = strtolower($this->val($row, ['Committed Member', 'Is Committed Member', 'Are you a Committed Member?']) ?? '');
            $inCommitAnswer   = strtolower($this->val($row, ['In Commitment Class', 'Currently in Commitment Class?']) ?? '');
            $medicalAnswer    = strtolower($this->val($row, ['Has Medical Condition', 'Do you have a medical condition?', 'Medical Condition?']) ?? '');

            $dob = $this->parseDate($this->val($row, ['DOB', 'Date of Birth', 'dob']));
            $membershipDate  = $this->parseDate($this->val($row, ['Date of Joining CUR', 'Membership Date', 'Date Joined']));
            $baptismDate     = $this->parseDate($this->val($row, ['Baptism Date', 'Date of Baptism']));

            User::create([
                'name'                       => $this->val($row, ['First Name', 'first name', 'firstname', 'First_Name']) ?? 'Unknown',
                'middle_name'                => $this->val($row, ['Middle Name', 'middle name']),
                'last_name'                  => $this->val($row, ['Last Name', 'last name', 'lastname', 'Surname']),
                'gender'                     => $this->mapGender($this->val($row, ['Gender', 'gender'])),
                'marital_status'             => $this->val($row, ['Marital Status', 'marital status', 'marital_status']),
                'email'                      => $email,
                'phone'                      => $this->val($row, ['Phone Number', 'Phone', 'phone', 'Mobile']),
                'address'                    => $this->val($row, ['Address', 'address', 'Physical Address']),
                'date_of_birth'              => $dob,
                'county'                     => $this->val($row, ['County of Residence', 'County', 'county']),
                'sub_county'                 => $this->val($row, ['Sub County', 'sub county', 'sub_county']),
                'sub_location'               => $this->val($row, ['Sub Location/Estate', 'Sub Location', 'Estate']),
                'salvation_date'             => $this->val($row, ['Month and year when of Salvation', 'Salvation Date', 'Month/Year of Salvation']),
                'is_born_again'              => in_array($bornAgainAnswer, ['yes', 'y', '1', 'true']),
                'is_baptized'                => in_array($baptizedAnswer, ['yes', 'y', '1', 'true']),
                'baptism_date'               => $baptismDate,
                'is_committed_member'        => in_array($committedAnswer, ['yes', 'y', '1', 'true']),
                'in_commitment_class'        => in_array($inCommitAnswer, ['yes', 'y', '1', 'true']),
                'membership_date'            => $membershipDate,
                'committed_date'             => $this->val($row, ['Month and year Committed', 'Committed Date']),
                'member_type'                => $this->val($row, ['Member Type', 'member type', 'member_type']),
                'office'                     => $this->val($row, ['Office', 'Church Office', 'office']),
                'department'                 => $this->val($row, ['Department', 'department']),
                'department2'                => $this->val($row, ['Department 2', 'Second Department', 'department2']),
                'department3'                => $this->val($row, ['Department 3', 'Third Department', 'department3']),
                'occupation'                 => $this->val($row, ['Career/Occupation', 'Occupation', 'Career', 'occupation']),
                'next_of_kin_name'           => $this->val($row, ['Next of Kin that can be reached in case of an emergency', 'Next of Kin', 'Next of Kin Name']),
                'next_of_kin_relationship'   => $this->val($row, ['Relationship with the next of Kin', 'Next of Kin Relationship', 'Relationship']),
                'next_of_kin_phone'          => $this->val($row, ['Phone Number of the Next of Kin', 'Next of Kin Phone']),
                'next_of_kin2_name'          => $this->val($row, ['Second Next of Kin', 'Next of Kin 2 Name', 'next_of_kin2_name']),
                'next_of_kin2_relationship'  => $this->val($row, ['Next of Kin 2 Relationship', 'Relationship 2']),
                'next_of_kin2_phone'         => $this->val($row, ['Next of Kin 2 Phone', 'Second Next of Kin Phone']),
                'belongs_to_home_cell'       => in_array($homeCellAnswer, ['yes', 'y', '1', 'true']),
                'home_cell'                  => $this->val($row, ['If Yes, which one?', 'Home Cell', 'home cell']),
                'assigned_to_deacon'         => in_array($deaconAnswer, ['yes', 'y', '1', 'true']),
                'deacon_name'                => $this->val($row, ['If yes, mention their name', 'Deacon Name', 'Deaconess Name', 'Deacon/Deaconess']),
                'transfer_type'              => $this->val($row, ['Transfer Type', 'transfer_type']),
                'transfer_church'            => $this->val($row, ['Transfer Church', 'Transferred From', 'transfer_church']),
                'has_medical_condition'      => in_array($medicalAnswer, ['yes', 'y', '1', 'true']),
                'medical_conditions'         => $this->val($row, ['Medical Conditions', 'medical_conditions']),
                'medications'                => $this->val($row, ['Medications', 'medications']),
                'allergies'                  => $this->val($row, ['Allergies', 'allergies']),
                'emergency_medical_contact'  => $this->val($row, ['Emergency Medical Contact', 'emergency_medical_contact']),
                'emergency_medical_phone'    => $this->val($row, ['Emergency Medical Phone', 'emergency_medical_phone']),
                'special_needs'              => $this->val($row, ['Special Needs', 'special_needs']),
                'role'                       => 'member',
                'password'                   => Hash::make(Str::random(32)),
                'must_change_password'       => true,
            ]);

            $imported++;
        }

        $msg = "Import complete: {$imported} members imported, {$skipped} skipped.";
        return redirect()->route('admin.members.index')
            ->with('success', $msg)
            ->with('import_errors', $errors);
    }

    private function parseDate(mixed $raw): ?string
    {
        if (!$raw) return null;
        try {
            if ($raw instanceof \DateTime) return $raw->format('Y-m-d');
            if (is_numeric($raw)) return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($raw)->format('Y-m-d');
            return date('Y-m-d', strtotime($raw)) ?: null;
        } catch (\Exception) {
            return null;
        }
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
