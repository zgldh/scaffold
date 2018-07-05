<?php

use Illuminate\Database\Seeder;

class ScaffoldInitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
//        $this->call(UploadsTableSeeder::class);
    }
}
