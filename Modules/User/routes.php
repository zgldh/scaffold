<?php
$api->post('user/role/copy', '\Modules\User\Controllers\RoleController@copy');

$api->put('user/permission/{id}/sync_roles', '\Modules\User\Controllers\PermissionController@syncRoles');

$api->post('auth/signup', '\Modules\User\Controllers\Auth\SignUpController@signUp');
$api->post('auth/login', '\Modules\User\Controllers\Auth\LoginController@login');
$api->post('auth/recovery', '\Modules\User\Controllers\Auth\ForgotPasswordController@sendResetEmail');
$api->post('auth/reset', '\Modules\User\Controllers\Auth\ResetPasswordController@resetPassword');
$api->post('auth/logout', '\Modules\User\Controllers\Auth\LogoutController@logout');
$api->post('auth/refresh', '\Modules\User\Controllers\Auth\RefreshController@refresh');

$api->post('user/role/bundle', '\Modules\User\Controllers\RoleController@bundle');
$api->resource('user/role', '\Modules\User\Controllers\RoleController');
$api->post('user/permission/bundle', '\Modules\User\Controllers\PermissionController@bundle');
$api->resource('user/permission', '\Modules\User\Controllers\PermissionController');
$api->post('user/bundle', '\Modules\User\Controllers\UserController@bundle');
$api->get('user/current', '\Modules\User\Controllers\UserController@current');
$api->put('user/password', '\Modules\User\Controllers\UserController@updateMyPassword');

$api->put('user/mobile', '\Modules\User\Controllers\UserController@putMobile');
$api->put('user/gender', '\Modules\User\Controllers\UserController@putGender');
$api->put('user/password', '\Modules\User\Controllers\UserController@putPassword');
$api->post('user/avatar', '\Modules\User\Controllers\UserController@postAvatar');
$api->resource('user', '\Modules\User\Controllers\UserController');
