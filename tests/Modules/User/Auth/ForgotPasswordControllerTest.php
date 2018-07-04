<?php

namespace Tests\Modules\User\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\User\Models\User;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
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

    public function testForgotPasswordRecoverySuccessfully()
    {
        $this->post('api/auth/recovery', [
            'email' => 'test@email.com'
        ])->assertJson([
            'status' => 'ok'
        ])->isOk();
    }

    public function testForgotPasswordRecoveryReturnsUserNotFoundError()
    {
        $this->post('api/auth/recovery', [
            'email' => 'unknown@email.com'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(404);
    }

    public function testForgotPasswordRecoveryReturnsValidationErrors()
    {
        $this->post('api/auth/recovery', [
            'email' => 'i am not an email'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(422);
    }
}
