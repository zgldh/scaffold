<?php

namespace Modules\User\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Auth\Events\Logout;

class LogoutController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::guard()->user();
        Auth::guard()->logout();
        event(new Logout($user));

        return response()
            ->json(['message' => 'Successfully logged out']);
    }
}
