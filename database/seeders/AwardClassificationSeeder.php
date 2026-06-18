<?php

namespace Database\Seeders;

use App\Models\AwardClassification;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class AwardClassificationSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        foreach ($this->institutionData()['award_classifications'] as $row) {
            AwardClassification::updateOrCreate(
                ['name' => $row['name']],
                [
                    'overall_gpa' => $row['overall_gpa'],
                    'high_gpa' => $row['high_gpa'],
                    'low_gpa' => $row['low_gpa'],
                ]
            );
        }
    }
}
