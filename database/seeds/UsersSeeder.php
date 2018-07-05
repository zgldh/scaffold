<?php

use Illuminate\Database\Seeder;
use Modules\User\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(100)->create();
    }
}
