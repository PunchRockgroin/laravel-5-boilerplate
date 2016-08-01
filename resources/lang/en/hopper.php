<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hopper Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in Hopper general items throughout the system.
    | Regardless where it is placed, a file and file entity item can be listed here so it is easily
    | found in a intuitive way.
    |
    */

    'backend' => [
        'admin' => [
            'title' => 'File Management',
            'index' => 'Dashboard',
            'create' => 'Create a new File',
            'edit' => 'Edit',
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
            'title' => 'Hopper',
            'index' => 'Dashboard',
            'create' => 'Create File',
        ],
    ],
];
