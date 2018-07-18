<?php

use Illuminate\Database\Seeder;
use Modules\User\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdminUser = User::find(1);
        $notifier = User::find(13);

        $notificationClass = null;
        for ($i = 0; $i < 50; $i++) {
            $notificationClass = rand(1, 10) < 5 ? \Modules\Notification\Notifications\Foo::class :
                \Modules\Notification\Notifications\Bar::class;

            $notifier = rand(1, 10) < 5 ? User::find(rand(2, 20)) : null;
            $notification = new $notificationClass($notifier);

            $superAdminUser->notify($notification);
        }
    }
}
