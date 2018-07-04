<?php

namespace Tests\Modules\User\Auth;

use Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testSignUpSuccessfully()
    {
        $this->post('api/auth/signup', [
            'name'     => 'Test User',
            'email'    => 'test@email.com',
            'password' => '123456'
        ])->assertJson([
            'status' => 'ok'
        ])->assertStatus(201);
    }

    public function testSignUpSuccessfullyWithTokenRelease()
    {
        Config::set('boilerplate.sign_up.release_token', true);

        $this->post('api/auth/signup', [
            'name'     => 'Test User',
            'email'    => 'test@email.com',
            'password' => '123456'
        ])->assertJsonStructure([
            'status', 'token'
        ])->assertJson([
            'status' => 'ok'
        ])->assertStatus(201);
    }

    public function testSignUpReturnsValidationError()
    {
        $this->post('api/auth/signup', [
            'name'  => 'Test User',
            'email' => 'test@email.com'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(422);
    }
}
