<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Database\Seeders\Concerns\LoadsSeedData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    use LoadsSeedData;

    public function run(): void
    {
        $course = Course::where('name', 'Bachelor in Computer Engineering')->firstOrFail();
        $password = bcrypt($this->institutionData()['default_password']);

        foreach ($this->institutionData()['students'] as $row) {
            Student::updateOrCreate(
                ['email' => $row['email']],
                [
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'],
                    'surname' => $row['surname'],
                    'registration_no' => $row['registration_no'],
                    'course_id' => $course->id,
                    'gender' => $row['gender'],
                    'dob' => $row['dob'],
                    'role' => 'Student',
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ]
            );
        }
    }
}
