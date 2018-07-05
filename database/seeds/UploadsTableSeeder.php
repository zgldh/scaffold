<?php

use Illuminate\Database\Seeder;
use Modules\User\Models\User;

class UploadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('name', 'admin')->first();

        $adminAvatar = new \Modules\Upload\Models\Upload();
        $adminAvatar->name = 'admin avatar';
        $adminAvatar->description = 'cool';
        $adminAvatar->disk = 'public';
        $adminAvatar->path = 'avatars/admin.png';
        $adminAvatar->size = '830';
        $adminAvatar->type = 'avatar';
        $adminAvatar->user_id = $user->id;
        $adminAvatar->uploadable_id = $user->id;
        $adminAvatar->uploadable_type = User::class;
        $adminAvatar->save();

        $user->avatar_id = $adminAvatar->id;
        $user->save();
    }
}
