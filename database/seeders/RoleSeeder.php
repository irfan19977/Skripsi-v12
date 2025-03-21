<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create([
            'name' => 'Admin'
        ]);
        $role2 = Role::create([
            'name' => 'Student'
        ]);
        $role3 = Role::create([
            'name' => 'Teacher'
        ]);
        $role4 = Role::create([
            'name' => 'Parent'
        ]);
    }
}
