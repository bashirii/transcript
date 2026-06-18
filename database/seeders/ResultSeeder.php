<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Module;
use App\Models\Result;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class ResultSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        $lecturer = User::where('email', 'h.fimbombaya@dit.test')->firstOrFail();

        foreach ($this->institutionData()['sample_results'] as $row) {
            $student = Student::where('email', $row['student_email'])->firstOrFail();
            $module = Module::where('code', $row['module_code'])->firstOrFail();
            $total = $row['c_a_marks'] + $row['f_e_marks'];

            $grade = Grade::where('low_marks', '<=', $total)
                ->where('high_marks', '>=', $total)
                ->first();

            Result::updateOrCreate(
                [
                    'student_reg_no' => $student->registration_no,
                    'module_code' => $module->code,
                ],
                [
                    'c_a_marks' => $row['c_a_marks'],
                    'f_e_marks' => $row['f_e_marks'],
                    'total_marks' => $total,
                    'grade_id' => $grade?->id,
                    'staff_id' => $lecturer->id,
                ]
            );
        }
    }
}
