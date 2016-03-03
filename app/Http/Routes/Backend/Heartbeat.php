<?php

Route::post('/', ['as' => 'backend.heartbeat.index', 'uses' => 'HeartbeatController@index']);
Route::get('/status', ['as' => 'backend.heartbeat.status', 'uses'=> 'HeartbeatController@status']);