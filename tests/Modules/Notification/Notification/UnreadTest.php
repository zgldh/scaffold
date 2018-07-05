<?php namespace Tests\Modules\Notification\Notification;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class UnreadTest extends TestCase
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

    public function testPutIdUnreadSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->put('api/notification/{id}/unread');
        $response->assertStatus(200);
    }

    public function testPutIdUnreadWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->put('api/notification/{id}/unread');
        $response->assertStatus(500);
    }
}
