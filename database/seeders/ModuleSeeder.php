<?php

namespace Database\Seeders;

use App\Models\Module;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        foreach ($this->curriculumData() as $row) {
            Module::updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'credit' => (string) $row['credit'],
                ]
            );
        }
    }
}
