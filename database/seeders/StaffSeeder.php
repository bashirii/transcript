<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StaffSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        $password = bcrypt($this->institutionData()['default_password']);

        foreach ($this->institutionData()['staff'] as $row) {
            $department = Department::where('name', $row['department'])->firstOrFail();

            User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?: '—',
                    'surname' => $row['surname'],
                    'gender' => $row['gender'],
                    'role' => $row['role'],
                    'department_id' => $department->id,
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ]
            );
        }
    }
}
