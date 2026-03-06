<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Buat Role
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate([
            'name' => 'super admin'
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'staff'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Assign Permission
        |--------------------------------------------------------------------------
        */

        // Super admin dapat semua permission
        $superAdmin->syncPermissions(Permission::all());
    }
}
