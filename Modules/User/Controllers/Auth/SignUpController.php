<?php

namespace Modules\User\Controllers\Auth;

use Config;
use App\User;
use Modules\User\Requests\Auth\SignUpRequest;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SignUpController extends Controller
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        $user = new User($request->all());
        if (!$user->save()) {
            throw new HttpException(500);
        }

        if (!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'status' => 'ok',
            'token'  => $token
        ], 201);
    }
}
