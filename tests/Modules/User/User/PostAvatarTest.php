<?php namespace Tests\Modules\User\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class PostAvatarTest extends TestCase
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

    public function testPostAvatarSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->post('api/user/avatar');
        $response->assertStatus(200);
    }

    public function testPostAvatarWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->post('api/user/avatar');
        $response->assertStatus(500);
    }
}
