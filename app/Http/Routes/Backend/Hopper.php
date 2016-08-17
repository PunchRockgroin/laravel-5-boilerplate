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
//           Route::get('/import/{model}', ['as' => 'backend.hopper.admin.import', 'uses' => 'HopperAdminController@import']);
            Route::post('/import/upload/{model}', ['as' => 'backend.hopper.admin.import.upload', 'uses' => 'HopperAdminController@importUpload']);
            Route::post('/import/process', ['as' => 'backend.hopper.admin.import.process', 'uses' => 'HopperAdminController@processUpload']);
			Route::get('/import/eventsessions', ['as' => 'backend.hopper.admin.import.eventsessions', 'uses' => 'HopperAdminController@importEventSessions']);
            Route::post('/update', ['as' => 'backend.hopper.admin.master_file_update', 'uses' => 'HopperAdminController@masterFileUpdate']); 
			
			Route::get('/fileopstest', ['as' => 'backend.hopper.admin.fileopstest', 'uses' => 'HopperAdminController@fileopstest']);
			
        });