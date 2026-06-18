<?php

namespace Database\Seeders;

use App\Models\Semister;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class SemisterSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        foreach ($this->institutionData()['semisters'] as $name) {
            Semister::updateOrCreate(['name' => $name]);
        }
    }
}
