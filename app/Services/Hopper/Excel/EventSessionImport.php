<?php
namespace App\Services\Hopper\Excel;

use App\Models\Hopper\EventSession;


/**
 * Description of EventSessionImport
 *
 * @author David Alberts
 */
class EventSessionImport extends \Maatwebsite\Excel\Files\ExcelFile {
    
    private $hoppereventsession;
    
    public function __construct(
            \Illuminate\Foundation\Application $app,
            \Maatwebsite\Excel\Excel $excel) {
        parent::__construct($app, $excel);
    }
        

    public function getFile()
    {
		$import_default =  env('HOPPER_STORAGE', storage_path() . '/app') . "/import" . '/import.xlsx';
		if(  file_exists( $import_default )){
			return $import_default;
		}
		return null;
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }
}