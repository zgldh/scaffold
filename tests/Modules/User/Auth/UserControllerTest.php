<?php

namespace Tests\Modules\User\Auth;

use Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\User\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $user = new User([
            'name'     => 'Test',
            'email'    => 'test@email.com',
            'password' => '123456'
        ]);

        $user->save();
    }

    public function testMe()
    {
        $response = $this->post('api/auth/login', [
            'email'    => 'test@email.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200);

        $responseJSON = json_decode($response->getContent(), true);
        $token = $responseJSON['token'];

        $this->get('api/user/current', [], [
            'Authorization' => 'Bearer ' . $token
        ])->assertJson([
            'success' => true,
            'data'    => [
                'name'  => 'Test',
                'email' => 'test@email.com'
            ]
        ])->isOk();
    }
}
