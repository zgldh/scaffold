<?php

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    Auth::routes();
    Route::get('/logout', 'Auth\LoginController@logout');

    Route::get('/', ['as' => 'dashboard.index', 'uses' => 'HomeController@index']);

    require base_path('$NAME$/Dashboard/routes.php');
    require base_path('$NAME$/User/routes.php');
    require base_path('$NAME$/Upload/routes.php');
    require base_path('$NAME$/ActionLog/routes.php');