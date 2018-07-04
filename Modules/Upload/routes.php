<?php

$api->post('upload/bundle', '\Modules\Upload\Controllers\UploadController@bundle');
$api->resource('upload', '\Modules\Upload\Controllers\UploadController');
