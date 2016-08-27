<?php

return [
	/*
      |--------------------------------------------------------------------------
      | Hopper Initialization
      |--------------------------------------------------------------------------
      |
      |
     */
	'seed_additional_users' => env('HOPPER_SEED_ADDITIONAL_USERS', false),
	/*
      |--------------------------------------------------------------------------
      | Hopper Locale
      |--------------------------------------------------------------------------
      |
      |
     */
    'event_timezone' => env('HOPPER_EVENT_TIMEZONE',  'UTC'),
    /*
      |--------------------------------------------------------------------------
      | Hopper Storage Path
      |--------------------------------------------------------------------------
      |
      |
     */
    'local_storage' => env('HOPPER_STORAGE',  storage_path().'/app'),
    /*
      |--------------------------------------------------------------------------
      | Hopper Master Storage Location
      |--------------------------------------------------------------------------
      | either dropbox or hopper!
      |
     */
    'master_storage' => env('HOPPER_MASTER_STORAGE',  'hopper'),
    /*
    /*
      |--------------------------------------------------------------------------
      | Hopper Master Storage Location
      |--------------------------------------------------------------------------
      | either dropbox or hopper!
      |
     */
    'working_storage' => env('HOPPER_WORKING_STORAGE',  'hopper'),
    /*
      |--------------------------------------------------------------------------
      | Enable Dropbox
      |--------------------------------------------------------------------------
      | Don't forget to set credentials at config.dropbox
      |
     */
    'dropbox_enable' => env('DROPBOX_ENABLE',  false),
    /*
    /*
      |--------------------------------------------------------------------------
      | Enable Dropbox Copy?
      |--------------------------------------------------------------------------
      | Don't forget to set credentials at config.dropbox and enable above
      |
     */
    'dropbox_copy' => env('DROPBOX_COPY',  false),
    /*
      |--------------------------------------------------------------------------
      | PDF Generator
      |--------------------------------------------------------------------------
      |
      | Use an internal (libreoffice) pdf generator or a external url source
      |
     */
    'pdf_generator' => env('HOPPER_PDF_GENERATOR', 'internal'),
    /*
      |--------------------------------------------------------------------------
      | LibreOffice Path
      |--------------------------------------------------------------------------
      |
      | Path to LibreOffice, if exists
      |
     */
    'libreoffice' => env('LIBREOFFICE',  false),
    /*
      |--------------------------------------------------------------------------
      | Imagick Path
      |--------------------------------------------------------------------------
      |
      | Path to Imagick, if exists
      |
     */
    'imagick_convert' => env('IMAGICK_CONVERT',  false),
    /*
      |--------------------------------------------------------------------------
      | External URL
      |--------------------------------------------------------------------------
      |
      | URL to a clone of the site living on a probably Linux based server
      |
     */
    'external_url' => env('HOPPER_EXTERNAL_URL', 'http://hopper.lightsourcecreative.com/'),
    /*
      |--------------------------------------------------------------------------
      | Valid upload Mimes
      |--------------------------------------------------------------------------
      |
      | Mime types that can legitimatley use the uploader(s)
      |
     */
    'checkin_upload_mimes' => env('HOPPER_CHECKIN_UPLOAD_MIMES', 'pdf,ppt,pptx,txt'),
    /*
      |--------------------------------------------------------------------------
      | Valid upload Mimes
      |--------------------------------------------------------------------------
      |
      | Mime types that can legitimatley use the uploader(s)
      |
     */
    'filenameparts' => ['sessionID', 'speaker', 'roomIDs', 'version', 'shareStatus'],
	/*
      |--------------------------------------------------------------------------
      | Use Queue for Filesystem/Etc
      |--------------------------------------------------------------------------
      |
      | Do we use syncronous file system ops or queued ops
      |
     */
    'use_queue' => env('HOPPER_USE_QUEUE', true),
	
	/*
      |--------------------------------------------------------------------------
      | Use Assignment System 
      |--------------------------------------------------------------------------
      |
      | Do we use an assignment system for assigning visits to Graphic operators?
      |
     */
    'use_assignments' => env('HOPPER_USE_ASSIGNMENTS', true),
	
	/*
      |--------------------------------------------------------------------------
      | Appearance Settings
      |--------------------------------------------------------------------------
     */
	
	'print' => [
		'enable' => env('HOPPER_PRINT_FORMS', false),
		'location' => env('HOPPER_PRINT_FORMS_LOCATION', 'internal'),
		'timing' => env('HOPPER_PRINT_FORMS_TIMING', false),
		'logo' => [
			'dataURI' => env('HOPPER_PRINT_FORMS_LOGO_DATAURI', 'data:image/png;base64,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')
		]
	],
	
	/*
      |--------------------------------------------------------------------------
      | Import Settings
      |--------------------------------------------------------------------------
     */
	
	'import' => [
		'required_headers' => ['session_id', 'checked_in', 'speakers', 'presentation_owner', 'approval_brand', 'approval_revrec', 'approval_legal' ]
	],
	
	/*
      |--------------------------------------------------------------------------
      | Use Dates
      |--------------------------------------------------------------------------
      |
      | Do we show and use dates in event sessions (may not be necessary anymore)
      |
     */
    'use_dates' => env('HOPPER_USE_DATES', true),
	/*
      |--------------------------------------------------------------------------
      | Report Only Mode
      |--------------------------------------------------------------------------
      |
      | Puts hopper into Report Only mode (no file operations)
      |
     */
    'report_only_mode' => env('HOPPER_REPORT_ONLY_MODE', false),
];
