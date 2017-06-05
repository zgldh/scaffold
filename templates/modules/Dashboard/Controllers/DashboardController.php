<?php namespace $NAME$\Dashboard\Controllers;

use Illuminate\Http\Request;
use zgldh\Scaffold\AppBaseController;

class DashboardController extends AppBaseController
{

    public function __construct()
    {
        $this->middleware("auth:admin");
    }

    /**
     * Display a listing of the Certificate.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('$NAME$\Dashboard::index');
    }
}
