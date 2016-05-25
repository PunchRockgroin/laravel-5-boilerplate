<?php
Route::post('visit/find', 'VisitController@find')->name('visit.find');
Route::get('visit/stats', 'VisitController@stats')->name('visit.stats');
Route::resource('visit', 'VisitController', [
    'except' => ['create', 'store', 'destroy'],
    'names' => [
        'index' => 'visit.index',
        'show' => 'visit.show',
        'edit' => 'visit.edit',
        'update' => 'visit.update',
    ]
]);