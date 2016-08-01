<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EventSession Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in file and file entity items throughout the system.
    | Regardless where it is placed, a file and file entity item can be listed here so it is easily
    | found in a intuitive way.
    |
    */

    'backend' => [
		'name' => 'Visit',
        'admin' => [
            'title' => 'Visit Management',
            'index' => 'Dashboard',
            'create' => 'Create a new Visit',
            'edit' => 'Edit',
			'invoice' => 'Invoice',
        ],
        'form' => [
            'filename' => [
                'label' => 'File Name',
                'placeholder' => 'Use <session_id>_etc',
                'help_block' => 'Enter a filename',
            ],
            'next_version' => [
                'label' => 'Next Version',
                'help_block' => 'Choose the next version number',
            ],
            
        ],
        'sidebar' => [
            'title' => 'Visits',
            'index' => 'Dashboard',
            'create' => 'Create Visit',
        ],
		'dashboard' => [
			'my_assignments' => 'My Assignments'
		]
    ],
];
