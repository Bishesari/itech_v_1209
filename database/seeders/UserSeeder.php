<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['user_name' => 'Yasser.Bishesari', 'password' => '403Institute$#'],
        ];
        foreach ($users as $data) {
            User::create($data);
        }
    }
}
