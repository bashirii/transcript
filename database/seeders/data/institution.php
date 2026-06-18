<?php

/**
 * DIT institutional reference data (prospectus 2025/2026).
 */

return [
    'default_password' => '12345',

    'award_classifications' => [
        ['name' => 'Certificate', 'overall_gpa' => 2, 'high_gpa' => 5, 'low_gpa' => 1],
        ['name' => 'Ordinary Diploma', 'overall_gpa' => 2, 'high_gpa' => 5, 'low_gpa' => 1],
        ['name' => 'Bachelor', 'overall_gpa' => 4, 'high_gpa' => 5, 'low_gpa' => 2],
        ['name' => 'Master', 'overall_gpa' => 4, 'high_gpa' => 5, 'low_gpa' => 3],
    ],

    'nta_levels' => [
        ['name' => 'NTA Level 4', 'award' => 'Certificate'],
        ['name' => 'NTA Level 5', 'award' => 'Certificate'],
        ['name' => 'NTA Level 6', 'award' => 'Ordinary Diploma'],
        ['name' => 'NTA Level 7', 'award' => 'Bachelor'],
        ['name' => 'NTA Level 8', 'award' => 'Bachelor'],
        ['name' => 'NTA Level 9', 'award' => 'Master'],
    ],

    'departments' => [
        'Computer Studies',
        'General Studies',
        'Electronics and Telecommunication',
        'Electrical Engineering',
        'Mechanical Engineering',
        'Civil Engineering',
    ],

    'grades' => [
        ['name' => 'A', 'high_marks' => 100, 'low_marks' => 75, 'point' => 5],
        ['name' => 'B+', 'high_marks' => 74, 'low_marks' => 65, 'point' => 4],
        ['name' => 'B', 'high_marks' => 64, 'low_marks' => 55, 'point' => 3],
        ['name' => 'C', 'high_marks' => 54, 'low_marks' => 45, 'point' => 2],
        ['name' => 'D', 'high_marks' => 44, 'low_marks' => 35, 'point' => 1],
        ['name' => 'F', 'high_marks' => 34, 'low_marks' => 0, 'point' => 0],
    ],

    'courses' => [
        [
            'name' => 'Bachelor in Computer Engineering',
            'duration' => 4,
            'department' => 'Computer Studies',
            'nta_level' => 'NTA Level 7',
        ],
    ],

    'semisters' => [
        'Year 1 Semester 1',
        'Year 1 Semester 2',
        'Year 2 Semester 1',
        'Year 2 Semester 2',
        'Year 3 Semester 1',
        'Year 3 Semester 2',
        'Year 4 Semester 1',
        'Year 4 Semester 2',
    ],

    'academic_year' => [
        'year_of_studies' => '2025/2026',
        'current_semister' => 'Year 4 Semester 1',
        'progress_status' => false,
    ],

    'staff' => [
        [
            'first_name' => 'Bashir',
            'middle_name' => 'Basso',
            'surname' => 'Maduka',
            'email' => 'admin@dit.test',
            'gender' => 'male',
            'role' => 'Admin',
            'department' => 'Computer Studies',
        ],
        [
            'first_name' => 'Haji',
            'middle_name' => 'S.',
            'surname' => 'Fimbombaya',
            'email' => 'h.fimbombaya@dit.test',
            'gender' => 'male',
            'role' => 'Lecturer',
            'department' => 'Computer Studies',
        ],
        [
            'first_name' => 'S.',
            'middle_name' => 'M.',
            'surname' => 'Wambura',
            'email' => 's.wambura@dit.test',
            'gender' => 'male',
            'role' => 'Lecturer',
            'department' => 'Computer Studies',
        ],
        [
            'first_name' => 'Juma',
            'middle_name' => '',
            'surname' => 'Juma',
            'email' => 'juma.lecturer@dit.test',
            'gender' => 'male',
            'role' => 'Lecturer',
            'department' => 'Computer Studies',
        ],
    ],

    'students' => [
        [
            'first_name' => 'Bashir',
            'middle_name' => 'Idd',
            'surname' => 'Juma',
            'registration_no' => 2102302112345,
            'email' => 'bashiri@dit.test',
            'gender' => 'male',
            'dob' => '1996-02-15',
        ],
        [
            'first_name' => 'Elibariki',
            'middle_name' => 'Basso',
            'surname' => 'Sulle',
            'registration_no' => 2102302221363,
            'email' => 'elibariki@dit.test',
            'gender' => 'male',
            'dob' => '2002-02-14',
        ],
        [
            'first_name' => 'George',
            'middle_name' => 'Maduka',
            'surname' => 'George',
            'registration_no' => 2102302229565,
            'email' => 'george@dit.test',
            'gender' => 'male',
            'dob' => '1998-02-14',
        ],
    ],

    'lecturer_modules' => [
        'h.fimbombaya@dit.test' => ['COU 08105', 'COU 08102'],
        's.wambura@dit.test' => ['COU 08104', 'COU 08103'],
        'juma.lecturer@dit.test' => ['COU 08101', 'COU 08106'],
    ],

    'sample_results' => [
        ['student_email' => 'bashiri@dit.test', 'module_code' => 'COU 08105', 'c_a_marks' => 28, 'f_e_marks' => 55],
        ['student_email' => 'elibariki@dit.test', 'module_code' => 'COU 08105', 'c_a_marks' => 32, 'f_e_marks' => 60],
        ['student_email' => 'george@dit.test', 'module_code' => 'COU 08105', 'c_a_marks' => 24, 'f_e_marks' => 48],
        ['student_email' => 'bashiri@dit.test', 'module_code' => 'COU 08104', 'c_a_marks' => 30, 'f_e_marks' => 52],
        ['student_email' => 'elibariki@dit.test', 'module_code' => 'COU 08104', 'c_a_marks' => 26, 'f_e_marks' => 58],
    ],
];
