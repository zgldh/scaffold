<?php namespace Tests\Modules\Notification\Notification;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class NotificationDestroyTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

//      Prepare testing database environment.
        /**
         * $this->createTestUser();
         **/
    }

    public function testNotificationDestroySuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->delete('api/notification/{id}');
        $response->assertStatus(200);
    }

    public function testNotificationDestroyWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->delete('api/notification/{id}');
        $response->assertStatus(500);
    }
}
