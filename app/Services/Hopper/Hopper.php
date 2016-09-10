<?php

namespace App\Services\Hopper;

use App\Services\Hopper\Contracts\HopperContract;
use Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon as Carbon;
use App\Services\Hopper\HopperFile;
use Vinkla\Pusher\PusherManager;
use App\Models\Hopper\EventSession;
use App\Models\Hopper\Visit;
use App\Models\Hopper\FileEntity;

class Hopper implements HopperContract {

    public function blastOff() {

        return 'Houston, we have ignition';
    }

    public function groupedHistory($history = [], $date = 'd-M-y') {
        $History = collect($history);
        $GroupedHistory = $History->groupBy(function($item) use ($date) {
            return \Carbon\Carbon::parse($item['timestamp']['date'])->format($date);
        });
        return $GroupedHistory;
    }
    
    public function getCurrentVersion($currentFileName = null) {
        //        $currentFileParts = pathinfo($currentFileName)['filename'];
        $currentFileParts = pathinfo($currentFileName, PATHINFO_FILENAME);
        $currentFileNameParts = explode('_', $currentFileParts);
        if (empty($currentFileNameParts)) {
            return false;
        }
		//We only care about the end
		$version = collect($currentFileNameParts)->take(-1)->implode('');

        //If there is no file in Master but placeholer LCCNOFILE is there
        if ($version === 'LCCNOFILE') {
            //The Next version is 7
            $currentVersion = 7;
        } else { //Do the usual thing
            $currentVersion = (int) preg_replace("/[^0-9]/", '', $version);
        }
        return str_pad($currentVersion, 2, '0', STR_PAD_LEFT);
    }

    public function renameFileVersion($currentFileName = null, $nextVersion = null, $currentFileExtension = null) {
		
        if ($currentFileExtension === null) {
            $currentFileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);
        }
        $currentFileNameArray = $this->getFileParts($currentFileName);
		if(empty($currentFileNameArray)){
			return false;
		}
		//Pop the end off
		$currentFileNameArray->pop();
		//Put the New version at the end
		$currentFileNameArray->put('version', config('hopper.version_prefix', 'LCC') . $nextVersion);
		//Merge to new file name
		$newFileName = $currentFileNameArray->implode('_') . '.' . $currentFileExtension;		
		
        return $newFileName;
    }
	
	public function getFileParts($currentFileParts){

        $fileNameArrayParts = config('hopper.filenameparts');
        $currentFileNameParts = explode('_', pathinfo($currentFileParts, PATHINFO_FILENAME) );
        $currentFileNameArray = [];
        if(count($currentFileNameParts) < 3){
            //Probably a non-session file
            return $currentFileParts;
        }
        
        foreach($fileNameArrayParts as $key => $partname){
            if(array_key_exists($key, $currentFileNameParts)){
                $currentFileNameArray[$partname] = $currentFileNameParts[$key];
            }else{
                $currentFileNameArray[$partname] = null;
            }
        }
        
        return collect($currentFileNameArray);
    }
    
    
    public function parseDateTimeForDisplay($date, $format = null, $timezone = null){

         if($format === null){
             $format = config('hopper.event_datetime_display', 'm/d/y h:i A');
         }
         if($timezone === null){
             $timezone = config('hopper.event_timezone', 'UTC');
         }
        
        return \Carbon\Carbon::parse($date)->timezone($timezone)->format($format);
        
    }

}
