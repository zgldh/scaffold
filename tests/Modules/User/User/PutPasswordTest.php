<?php namespace Tests\Modules\User\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class PutPasswordTest extends TestCase
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

    public function testPutPasswordSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $newPassword = str_random(6);
        $response = $request->put('api/user/password', [
            'oldPassword'           => 'secret',
            'password'              => $newPassword,
            'password_confirmation' => $newPassword
        ]);
        $response->assertStatus(200);
    }

    public function testPutPasswordWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $newPassword = str_random(2);
        $response = $request->put('api/user/password', [
            'oldPassword'           => 'secret',
            'password'              => $newPassword,
            'password_confirmation' => $newPassword
        ]);
        $response->assertStatus(422);

        $newPassword = str_random(6);
        $response = $request->put('api/user/password', [
            'oldPassword'           => 'secret',
            'password'              => $newPassword,
            'password_confirmation' => $newPassword . '123'
        ]);
        $response->assertStatus(422);
    }
}
