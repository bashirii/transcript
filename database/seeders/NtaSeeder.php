<?php

namespace Database\Seeders;

use App\Models\AwardClassification;
use App\Models\NTA;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class NtaSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        foreach ($this->institutionData()['nta_levels'] as $row) {
            $award = AwardClassification::where('name', $row['award'])->firstOrFail();

            NTA::updateOrCreate(
                ['name' => $row['name']],
                ['award_id' => $award->id]
            );
        }
    }
}
