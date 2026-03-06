<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Buat User Admin
        |--------------------------------------------------------------------------
        */

        $user = User::firstOrCreate(
            [
                'email' => 'haidar17ali@gmail.com'
            ],
            [
                'name' => 'username',
                'password' => Hash::make('jufrie07'),
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Assign Role
        |--------------------------------------------------------------------------
        */

        $user->assignRole('super admin');
    }
}
