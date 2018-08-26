<?php
$api->get('setting', '\Modules\Setting\Controllers\SettingController@index');
$api->put('setting', '\Modules\Setting\Controllers\SettingController@updateAll');
$api->put('setting/{name}', '\Modules\Setting\Controllers\SettingController@update');
