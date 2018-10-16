<?php namespace Tests\Modules\Notification\Notification;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class NotificationDestroyTest extends BaseTest
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
    }

    public function testNotificationDestroySuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $notification = $this->sendNotification($user);
        $request = $this->actingAs($user, 'api');
        $response = $request->delete('api/notification/' . $notification->id);
        $response->assertStatus(200);
    }

    public function testNotificationDestroyWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->delete('api/notification/{id}');
        $response->assertStatus(500);
    }
}
