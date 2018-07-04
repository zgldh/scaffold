<?php namespace Tests\Modules\User\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class PutGenderTest extends TestCase
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

    public function testPutGenderSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->put('api/user/gender');
        $response->assertStatus(200);
    }

    public function testPutGenderWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->put('api/user/gender');
        $response->assertStatus(500);
    }
}
