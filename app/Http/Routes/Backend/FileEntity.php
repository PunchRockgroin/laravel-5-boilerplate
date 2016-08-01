<?php

//Route::model('fileentity', 'FileEntity');

Route::post('files/upload', 'FileEntityController@upload')->name('admin.fileentity.upload');
Route::post('files/nextversion', 'FileEntityController@nextVersion')->name('admin.fileentity.nextversion');
Route::any('files/data', 'FileEntityController@anyData')->name('admin.fileentity.data');

//Resource Routes
Route::resource('files', 'FileEntityController', ['names' => [
    'index' => 'admin.fileentity.index',
    'create' => 'admin.fileentity.create',
    'store' => 'admin.fileentity.store',
    'show' => 'admin.fileentity.show',
    'edit' => 'admin.fileentity.edit',
    'update' => 'admin.fileentity.update',
    'destroy' => 'admin.fileentity.destroy',
]]);
