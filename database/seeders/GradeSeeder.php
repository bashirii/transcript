<?php

namespace Database\Seeders;

use App\Models\Grade;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        foreach ($this->institutionData()['grades'] as $row) {
            Grade::updateOrCreate(
                ['name' => $row['name']],
                [
                    'high_marks' => $row['high_marks'],
                    'low_marks' => $row['low_marks'],
                    'point' => $row['point'],
                ]
            );
        }
    }
}
