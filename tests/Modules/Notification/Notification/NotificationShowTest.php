<?php namespace Tests\Modules\Notification\Notification;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class NotificationShowTest extends TestCase
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

    public function testNotificationShowSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->get('api/notification/{id}');
        $response->assertStatus(200);
    }

    public function testNotificationShowWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->get('api/notification/{id}');
        $response->assertStatus(500);
    }
}
