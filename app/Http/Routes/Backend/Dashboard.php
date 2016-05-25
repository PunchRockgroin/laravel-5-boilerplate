<?php

Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');
Route::get('dashboard/data', 'DashboardController@data')->name('admin.dashboard.data');
Route::get('dashboard/heartbeat/{id}', 'DashboardController@userHeartbeat')->name('admin.dashboard.user');