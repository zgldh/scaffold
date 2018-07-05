<?php
$api->post('notification/bundle', '\Modules\Notification\Controllers\NotificationController@bundle');


$api->get('notification/read_latest/{last_created_at}', '\Modules\Notification\Controllers\NotificationController@getReadLatest');
$api->get('notification', '\Modules\Notification\Controllers\NotificationController@index');
$api->get('notification/{id}', '\Modules\Notification\Controllers\NotificationController@show');
$api->put('notification/{id}/read', '\Modules\Notification\Controllers\NotificationController@read');
$api->put('notification/read_all', '\Modules\Notification\Controllers\NotificationController@readAll');
$api->put('notification/{id}/unread', '\Modules\Notification\Controllers\NotificationController@unread');
$api->delete('notification/{id}', '\Modules\Notification\Controllers\NotificationController@destroy');
