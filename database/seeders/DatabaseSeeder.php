<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run: php artisan db:seed
     * Fresh: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            AwardClassificationSeeder::class,
            NtaSeeder::class,
            DepartmentSeeder::class,
            GradeSeeder::class,
            SemisterSeeder::class,
            ModuleSeeder::class,
            CourseSeeder::class,
            AcademicYearProgressSeeder::class,
            CourseSemisterModulesSeeder::class,
            StaffSeeder::class,
            StudentSeeder::class,
            LecturerModuleSeeder::class,
            StudentModuleSeeder::class,
            ResultSeeder::class,
        ]);
    }
}
