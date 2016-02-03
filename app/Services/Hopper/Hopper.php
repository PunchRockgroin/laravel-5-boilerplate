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
        if(!$currentFileName){
            return false;
        }
        $currentFileParts = pathinfo($currentFileName)['filename'];
        $currentFileNameParts = explode('_', $currentFileParts);
    		if(empty($currentFileNameParts)){
    			return false;
    		}
        //If there is no file in Master but placeholer LCCNOFILE is there
        if($currentFileNameParts[3] === 'LCCNOFILE'){
            //The Next version is 7
            $currentVersion = 7;
        }else{ //Do the usual thing
            $currentVersion = (int) preg_replace("/[^0-9]/", '', $currentFileNameParts[3]);
        }
        return str_pad($currentVersion, 2, '0', STR_PAD_LEFT);
    }

    public function renameFileVersion($currentFileName = null, $nextVersion = null, $currentFileExtension = null) {
        if(!$currentFileName || !$nextVersion){
            return false;
        }
        $currentFileParts = pathinfo($currentFileName)['filename'];
        if ($currentFileExtension === null) {
            $currentFileExtension = pathinfo($currentFileName)['extension'];
        }
        $currentFileNameParts = explode('_', $currentFileParts);
        $currentFileNameArray = [
            'sessionID' => $currentFileNameParts[0],
            'speaker' => $currentFileNameParts[1],
            'roomIDs' => $currentFileNameParts[2],
            'version' => $currentFileNameParts[3],
            'shareStatus' => $currentFileNameParts[4]
        ];

        $newFileName = $currentFileNameArray['sessionID']
                . '_' . $currentFileNameArray['speaker']
                . '_' . $currentFileNameArray['roomIDs']
                . '_LCC' . $nextVersion
                . '_' . $currentFileNameArray['shareStatus']
                . '.' . $currentFileExtension;

        return $newFileName;
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
