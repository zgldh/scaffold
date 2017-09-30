<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app');
    }

    public function getLanguage(Request $request, $modules = 'scaffold')
    {
        $modules = preg_split('/,/', $modules);
        $result = [];
        foreach ($modules as $module) {
            $languages = __($module . '::t');
            $result[$module] = $languages;
        }
        return $result;
    }
}
