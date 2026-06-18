<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\NTA;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        foreach ($this->institutionData()['courses'] as $row) {
            $department = Department::where('name', $row['department'])->firstOrFail();
            $nta = NTA::where('name', $row['nta_level'])->firstOrFail();

            Course::updateOrCreate(
                ['name' => $row['name']],
                [
                    'duration' => $row['duration'],
                    'department_id' => $department->id,
                    'n_t_a_level_id' => $nta->id,
                ]
            );
        }
    }
}
