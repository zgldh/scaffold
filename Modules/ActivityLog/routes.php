<?php

$api->post('activity_log/bundle', '\Modules\ActivityLog\Controllers\ActivityLogController@bundle');
$api->resource('activity_log', '\Modules\ActivityLog\Controllers\ActivityLogController');
