<?php namespace Tests\Modules\Post\Post;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class PostStoreTest extends TestCase
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

    public function testPostStoreSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->post('api/post');
        $response->assertStatus(200);
    }

    public function testPostStoreWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->post('api/post');
        $response->assertStatus(500);
    }
}
