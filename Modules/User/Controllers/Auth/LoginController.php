<?php

namespace Modules\User\Controllers\Auth;

use Modules\User\Requests\Auth\LoginRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;

class LoginController extends Controller
{
    /**
     * Log the user in
     *
     * @param LoginRequest $request
     * @param JWTAuth $JWTAuth
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = \JWTAuth::attempt($credentials);

            if (!$token) {
                throw new AccessDeniedHttpException(__('auth.failed'));
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        return response()
            ->json([
                'status'     => 'ok',
                'token'      => $token,
                'expires_in' => Auth::guard()->factory()->getTTL() * 60
            ]);
    }
}
