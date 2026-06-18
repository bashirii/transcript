<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Semister;
use App\Models\User;
use App\Models\lecturer_module;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;

class LecturerModuleSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        $semister = Semister::where('name', 'Year 4 Semester 1')->firstOrFail();

        foreach ($this->institutionData()['lecturer_modules'] as $email => $moduleCodes) {
            $lecturer = User::where('email', $email)->firstOrFail();

            foreach ($moduleCodes as $code) {
                $module = Module::where('code', $code)->firstOrFail();

                lecturer_module::updateOrCreate(
                    [
                        'lecturer_id' => $lecturer->id,
                        'module_id' => $module->id,
                        'semister_id' => $semister->id,
                    ],
                    ['complete_status' => false]
                );
            }
        }
    }
}
