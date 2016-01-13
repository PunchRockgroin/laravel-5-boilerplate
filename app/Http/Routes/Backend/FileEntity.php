<?php

Route::get('files', 'FileEntityController@index')->name('admin.fileentity.index');
Route::get('files/create', 'FileEntityController@create')->name('admin.fileentity.create');
Route::get('files/store', 'FileEntityController@store')->name('admin.fileentity.store');
Route::get('files/show/{id}', 'FileEntityController@show')->name('admin.fileentity.show');
Route::get('files/edit/{id}', 'FileEntityController@edit')->name('admin.fileentity.edit');
Route::get('files/update/{id}', 'FileEntityController@update')->name('admin.fileentity.update');
Route::get('files/destroy/{id}', 'FileEntityController@destroy')->name('admin.fileentity.destroy');

Route::post('files/upload', 'FileEntityController@upload')->name('admin.fileentity.upload');
