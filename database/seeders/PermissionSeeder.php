<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //dashboard
        Permission::create(['name' => 'dashboard.index']);

        //roles
        Permission::create(['name' => 'roles.index']);
        Permission::create(['name' => 'roles.create']);
        Permission::create(['name' => 'roles.edit']);

        //permissions
        Permission::create(['name' => 'permissions.index']);
        Permission::create(['name' => 'permissions.create']);
        Permission::create(['name' => 'permissions.edit']);

        //user
        Permission::create(['name' => 'users.index']);
        Permission::create(['name' => 'users.create']);
        Permission::create(['name' => 'users.edit']);

        //class
        Permission::create(['name' => 'class.index']);
        Permission::create(['name' => 'class.create']);
        Permission::create(['name' => 'class.edit']);

        //subject
        Permission::create(['name' => 'subjects.index']);
        Permission::create(['name' => 'subjects.create']);
        Permission::create(['name' => 'subjects.edit']);

        //attendances
        Permission::create(['name' => 'attendances.index']);
        Permission::create(['name' => 'attendances.create']);
        Permission::create(['name' => 'attendances.edit']);
        
        //Teacher
        Permission::create(['name' => 'teachers.index']);
        Permission::create(['name' => 'students.index']);

        //Schedule
        Permission::create(['name' => 'schedules.index']);
        Permission::create(['name' => 'schedules.create']);
        Permission::create(['name' => 'schedules.edit']);

        // Assign all permissions to role 1 (Super Admin)
        $role = Role::find(1);
        $permissions = Permission::all();
 
        $role->syncPermissions($permissions);
 

        // Assign schedules.index permission to role 2
        $role2 = Role::find(2);
        $role2->syncPermissions(['schedules.index']);

        // Assign specific permissions to role 3
        $role3 = Role::find(3);
        $role3Permissions = Permission::whereIn('name', [
            'attendances.index',
            'attendances.create',
            'attendances.edit',
            'schedules.index'
        ])->get();
        $role3->syncPermissions($role3Permissions);

        $user = User::where('name', 'Admin')->first();
        $user->assignRole($role->name);
    }
}
