<?php
Route::post('visit/find', 'VisitController@find')->name('admin.visit.find');
Route::get('visit/stats', 'VisitController@stats')->name('admin.visit.stats');
Route::resource('visit', 'VisitController', [
    'except' => ['create', 'store',],
    'names' => [
        'index' => 'admin.visit.index',
        'show' => 'admin.visit.show',
        'edit' => 'admin.visit.edit',
        'update' => 'admin.visit.update',
        'destroy' => 'admin.visit.destroy',
    ]
]);