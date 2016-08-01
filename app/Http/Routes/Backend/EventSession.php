<?php

Route::resource('eventsession', 'EventSessionController', ['names' => [
    'index' => 'admin.eventsession.index',
    'create' => 'admin.eventsession.create',
    'store' => 'admin.eventsession.store',
    'show' => 'admin.eventsession.show',
    'edit' => 'admin.eventsession.edit',
    'update' => 'admin.eventsession.update',
    'destroy' => 'admin.eventsession.destroy',
]]);

