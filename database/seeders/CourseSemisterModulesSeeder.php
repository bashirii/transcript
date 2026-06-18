<?php

namespace Database\Seeders;

use App\Models\academicYearProgress;
use App\Models\Course;
use App\Models\Module;
use App\Models\Semister;
use App\Models\course_semister_modules;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class CourseSemisterModulesSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        $course = Course::where('name', 'Bachelor in Computer Engineering')->firstOrFail();
        $acYear = academicYearProgress::where('year_of_studies', '2025/2026')->firstOrFail();
        $semisterNames = $this->institutionData()['semisters'];

        $modulesBySemister = [];
        foreach ($this->curriculumData() as $row) {
            $index = $this->semisterIndex($row['year'], $row['semester']);
            $modulesBySemister[$index][] = $row['code'];
        }

        foreach ($modulesBySemister as $index => $codes) {
            $semister = Semister::where('name', $semisterNames[$index])->firstOrFail();
            $moduleIds = Module::whereIn('code', $codes)->pluck('id')->map(fn ($id) => (string) $id)->values()->all();

            course_semister_modules::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'semister_id' => $semister->id,
                    'ac_year_id' => $acYear->id,
                ],
                ['module_ids' => json_encode($moduleIds)]
            );
        }
    }
}
