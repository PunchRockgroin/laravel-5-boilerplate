<?php

Route::group([
            'prefix' => 'hopper',
                ], function () {
            Route::get('/', ['as' => 'backend.hopper.admin.index', 'uses' => 'HopperAdminController@index']);
            Route::get('/update', ['as' => 'backend.hopper.admin.update', 'uses' => 'HopperAdminController@update']);
            Route::get('/export/{model}', [
                'as' => 'backend.hopper.admin.export', 
                'uses' => 'HopperAdminController@export'
            ]);
           Route::get('/import/{model}', ['as' => 'backend.hopper.admin.import', 'uses' => 'HopperAdminController@import']);
           Route::post('/update', ['as' => 'backend.hopper.admin.master_file_update', 'uses' => 'HopperAdminController@masterFileUpdate']); 
        });