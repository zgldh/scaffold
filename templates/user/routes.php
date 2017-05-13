<?php

Route::resource('user', '\$NAME$\User\Controllers\UserController');

Route::resource('user/role', '\$NAME$\User\Controllers\RoleController');

Route::resource('user/permission', '\$NAME$\User\Controllers\PermissionController');
