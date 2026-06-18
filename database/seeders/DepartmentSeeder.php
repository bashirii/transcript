<?php

namespace Database\Seeders;

use App\Models\Department;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        foreach ($this->institutionData()['departments'] as $name) {
            Department::updateOrCreate(['name' => $name]);
        }
    }
}
