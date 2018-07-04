<?php namespace Tests\Modules\Notification\Notification;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class GetReadLatestTest extends TestCase
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

    public function testGetReadLatestSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->get('api/notification/read_latest/{last_created_at}');
        $response->assertStatus(200);
    }

    public function testGetReadLatestWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->get('api/notification/read_latest/{last_created_at}');
        $response->assertStatus(500);
    }
}
