<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\InstituteRoleUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'user_name' => 'Yasser.Bishesari',
            'password' => '403Institute$#',
        ]);
        InstituteRoleUser::create([
            'user_id' => $user->id,
            'role_id' => 2,
            'assigned_by' => 1,
        ]);

        $user->profile()->create([
            'identifier_type' => 'national_id',
            'n_code' => '2063531218',
        ]);
        $contact = Contact::firstOrCreate(['mobile_nu' => '09177755924', 'verified' => 1]);
        $user->contacts()->syncWithoutDetaching([$contact->id]);
        $contact = Contact::firstOrCreate(['mobile_nu' => '09034336111', 'verified' => 1]);
        $user->contacts()->syncWithoutDetaching([$contact->id]);

    }
}
