<?php
$api->post('post/bundle', '\Modules\Post\Controllers\PostController@bundle');
$api->resource('post', '\Modules\Post\Controllers\PostController');


