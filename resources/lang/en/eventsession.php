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
        'admin' => [
            'title' => 'Event Session Management',
            'index' => 'Dashboard',
            'create' => 'Create a new Event Session',
            'edit' => 'Edit',
        ],
        'form' => [
            'session_id' => [
                'label' => 'Session ID',
                'placeholder' => 'The Session ID',
                'help_block' => '',
            ],
            'checked_in' => [
                'label' => 'Checked In',
                'help_block' => '',
            ],
            'speakers' => [
                'label' => 'Speaker Names',
                'placeholder' => 'Primary Name, {Other Names}',
                'help_block' => 'Speaker names separated by a comma. The primary speaker should be first.',
            ],
            'onsite_phone' => [
                'label' => 'On-site Phone Number',
                'placeholder' => '',
                'help_block' => 'The contact number for the primary contact, usually the Presentation Owner',
            ],
            'presentation_owner' => [
                'label' => 'Presentation Owner',
                'placeholder' => '',
                'help_block' => 'The Presentation Owner is the person allowed to make changes to the presentation.',
            ],
            'check_in_datetime' => [
                'label' => 'Check-In Date and Time',
                'placeholder' => '',
                'help_block' => '',
            ],
            'approval_brand' => [
                'label' => 'Approved by Branding',
                'placeholder' => '',
                'help_block' => '',
            ],
            'approval_revrec' => [
                'label' => 'Approved by RevRec',
                'placeholder' => '',
                'help_block' => '',
            ],
            'approval_legal' => [
                'label' => 'Approved by Legal',
                'placeholder' => '',
                'help_block' => '',
            ],
            'dates_rooms' => [
                'label' => 'Dates and Rooms',
                'placeholder' => '',
                'help_block' => '',
            ],
            'dates_rooms_date' => [
                'label' => 'Date',
                'placeholder' => '',
                'help_block' => '',
            ],
            'dates_rooms_room_name' => [
                'label' => 'Room Name',
                'placeholder' => 'The Actual Room Name',
                'help_block' => '',
            ],
            'dates_rooms_room_id' => [
                'label' => 'Room ID',
                'placeholder' => 'ID Code for the Room',
                'help_block' => '',
            ],
            
            
        ],
        'sidebar' => [
            'title' => 'Event Sessions',
            'index' => 'Dashboard',
            'create' => 'Create Session',
        ],
    ],
];
