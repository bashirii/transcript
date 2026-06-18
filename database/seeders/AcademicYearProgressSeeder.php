<?php

namespace Database\Seeders;

use App\Models\academicYearProgress;
use App\Models\Semister;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class AcademicYearProgressSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        $config = $this->institutionData()['academic_year'];
        $semister = Semister::where('name', $config['current_semister'])->firstOrFail();

        academicYearProgress::updateOrCreate(
            ['year_of_studies' => $config['year_of_studies']],
            [
                'current_semister_id' => $semister->id,
                'progress_status' => $config['progress_status'],
            ]
        );
    }
}
