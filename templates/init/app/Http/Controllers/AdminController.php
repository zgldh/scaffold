<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $locale = null)
    {
        if ($locale) {
            \App::setLocale($locale);
        }
        $user = $request->user();
        if ($user && $user->isAdmin()) {
            return view('admin.index');
        }
        return redirect()->guest('/admin/login');
    }

    /**
     * Show login page
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm(Request $request)
    {
        $redirect = $request->get('redirect', '/admin');
        $data = [
            'redirect' => $redirect
        ];
        return view('admin.login', $data);
    }

    public function username()
    {
        return 'name';
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->guest('/admin');
    }

    public function redirectTo()
    {
        $path = \Request::get('redirect') ?: '/';
        return $path;
    }
}
