<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Student;
use App\Models\student_module;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class StudentModuleSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        $moduleCodes = collect($this->curriculumData())
            ->filter(fn ($row) => $row['year'] === 4 && $row['semester'] === 1)
            ->pluck('code')
            ->all();

        $modules = Module::whereIn('code', $moduleCodes)->get();

        foreach ($this->institutionData()['students'] as $row) {
            $student = Student::where('email', $row['email'])->firstOrFail();

            foreach ($modules as $module) {
                student_module::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'module_id' => $module->id,
                    ],
                    ['complete_status' => false]
                );
            }
        }
    }
}
