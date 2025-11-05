<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name_en' => 'Originator', 'name_fa' => 'موسس'],
            ['name_en' => 'Manager', 'name_fa' => 'مدیر'],
            ['name_en' => 'Assistant', 'name_fa' => 'مسئول اداری'],
            ['name_en' => 'Accountant', 'name_fa' => 'حسابدار'],
            ['name_en' => 'Teacher', 'name_fa' => 'مربی'],
            ['name_en' => 'Student', 'name_fa' => 'کارآموز'],
            ['name_en' => 'QuestionMaker', 'name_fa' => 'طراح سوال'],
            ['name_en' => 'QuestionAuditor', 'name_fa' => 'ممیز سوال'],
            ['name_en' => 'Examiner', 'name_fa' => 'آزمونگر'],
            ['name_en' => 'Marketer', 'name_fa' => 'بازاریاب'],
            ['name_en' => 'JobSeeker', 'name_fa' => 'کارجو'],
        ];

        foreach ($roles as $data) {
            Role::create($data);
        }
    }
}
