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
    Route::get('/lang/{module?}', ['as' => 'lang.get', 'uses' => 'HomeController@getLanguage']);
    Route::get('/admin/logout', ['as' => 'admin.login.page', 'uses' => 'AdminController@logout']);
    Route::get('/admin/login', ['as' => 'admin.login.page', 'uses' => 'AdminController@showLoginForm']);
    Route::post('/admin/login', ['as' => 'admin.login', 'uses' => 'AdminController@login']);
    Route::get('/admin/{lang?}', ['as' => 'admin.index', 'uses' => 'AdminController@index']);
