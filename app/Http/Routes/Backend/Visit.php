<?php
Route::post('visit/find', 'VisitController@find')->name('admin.visit.find');
Route::get('visit/stats', 'VisitController@stats')->name('admin.visit.stats');
Route::get('visit/datatable', 'VisitController@datatable')->name('admin.visit.datatable');
Route::get('visit/assignments', 'VisitController@assignments')->name('admin.visit.assignments');
Route::get('visit/myassignments', 'VisitController@myassignments')->name('admin.visit.myassignments');
Route::get('visit/unassigned', 'VisitController@unassigned')->name('admin.visit.unassigned');
Route::post('visit/assign/{id}', 'VisitController@assignUser')->name('admin.visit.assign');
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