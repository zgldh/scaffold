<?php
$api->post('setting/bundle', '\Modules\Setting\Controllers\SettingController@bundle');
$api->resource('setting', '\Modules\Setting\Controllers\SettingController');
