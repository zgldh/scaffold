<?php

use Illuminate\Database\Seeder;
use Modules\User\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdminUser = User::firstOrNew(['name' => 'admin']);
        $superAdminUser->email = 'admin@email.com';
        $superAdminUser->password = '123456';
        $superAdminUser->save();

        $superAdminRole = \Modules\User\Models\Role::firstOrNew(['name' => 'super-admin']);
        $superAdminRole->label = '超级管理员';
        $superAdminRole->save();
        if (!$superAdminUser->hasRole($superAdminRole)) {
            $superAdminUser->assignRole($superAdminRole);
        }

        $permissions = \Modules\User\Models\Permission::all();
        foreach ($permissions as $permission) {
            $superAdminRole->givePermissionTo($permission);
        }

        $ordinaryRole = \Modules\User\Models\Role::firstOrNew(['name' => 'ordinary-user']);
        $ordinaryRole->label = '普通用户';
        $ordinaryRole->save();
    }
}
