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
            \Maatwebsite\Excel\Excel $excel,
            \App\Services\Hopper\HopperEventSession $hoppereventsession) {
        parent::__construct($app, $excel);
        $this->hoppereventsession = $hoppereventsession;
    }
        

    public function getFile()
    {
        return env('HOPPER_STORAGE', storage_path() . '/app') . "/import" . '/import.xlsx';
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }
    
    
    public function importChunked($chunk = 250){
        $count = 0;
        $this->chunk($chunk, function($results) use (&$count)
            {
                $results->each(function($row) use (&$count) {
                    if (!empty($row['id'])) {
                        $row['id'] = (int) $row['id'];
                    }
                    if (!empty($row['dates_rooms'])) {
                        $row['dates_rooms'] = $this->hoppereventsession->setFromDateTimesString($row['dates_rooms']);
                    }
                    $searchParams = [
                        'session_id' => $row['session_id'],
                    ];
//                    debugbar()->info($row->all());                
                    EventSession::firstOrNew($searchParams)->fill($row->all())->save();
                    $count++;
                });
            }, false);
        return $count;
    }

}