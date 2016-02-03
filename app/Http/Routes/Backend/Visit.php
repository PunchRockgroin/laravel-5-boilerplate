<?php

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