<?php

Route::group(['middleware' => 'web'], function() {
    /**
     * Switch between the included languages
     */
    Route::group(['namespace' => 'Language'], function () {
        require (__DIR__ . '/Routes/Language/Language.php');
    });

    Route::group(['namespace' => 'Pusher', 'prefix' => 'pusher'], function(){
			require (__DIR__ . '/Routes/Pusher/Pusher.php');
		});

    /**
     * Frontend Routes
     * Namespaces indicate folder structure
     */
    Route::group(['namespace' => 'Frontend'], function () {
        require (__DIR__ . '/Routes/Frontend/Frontend.php');
        require (__DIR__ . '/Routes/Frontend/Access.php');
    });
});

/**
 * Backend Routes
 * Namespaces indicate folder structure
 * Admin middleware groups web, auth, and routeNeedsPermission
 */
Route::group(['namespace' => 'Backend', 'prefix' => 'admin', 'middleware' => 'admin'], function () {
    /**
     * These routes need view-backend permission
     * (good if you want to allow more than one group in the backend,
     * then limit the backend features by different roles or permissions)
     *
     * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
     */
    require (__DIR__ . '/Routes/Backend/Dashboard.php');
    require (__DIR__ . '/Routes/Backend/Access.php');
    require (__DIR__ . '/Routes/Backend/LogViewer.php');

    require (__DIR__ . '/Routes/Backend/FileEntity.php');
    require (__DIR__ . '/Routes/Backend/EventSession.php');
    require (__DIR__ . '/Routes/Backend/Visit.php');
    require (__DIR__ . '/Routes/Backend/Hopper.php');
});

/**
* Unauthenticated Broadcasting
*/
Route::group(['namespace' => 'Backend', 'prefix' => 'heartbeat'], function(){
    require (__DIR__ . '/Routes/Backend/Heartbeat.php');
});

Route::group(['namespace' => 'Auth','prefix'=>'api'], function(){
	Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
	Route::get('something', function(){
		$user = \App\Models\Access\User\User::first();
		$token = JWTAuth::fromUser($user);
		return response()->json(compact('token'));
	});
});

Route::post('/notify-client', ['as' => 'backend.hopper.admin.notify-client', 'uses' => '\App\Http\Controllers\Backend\HopperAdminController@notifyClient']);
