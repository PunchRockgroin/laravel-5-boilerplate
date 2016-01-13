<?php

return [

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
    'checkin_upload_mimes' => env('HOPPER_CHECKIN_UPLOAD_MIMES', 'pdf,ppt,pptx'),
];
