<?php namespace Tests\Modules\Post\Post;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class PostDestroyTest extends TestCase
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

    public function testPostDestroySuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->delete('api/post/{id}');
        $response->assertStatus(200);
    }

    public function testPostDestroyWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->delete('api/post/{id}');
        $response->assertStatus(500);
    }
}
