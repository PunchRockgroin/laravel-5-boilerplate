<?php

namespace App\Services\Hopper;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Hopper\EventSession;
use App\Services\Hopper\HopperVisit;
use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Contracts\HopperFileContract;
use App\Events\Backend\Hopper\EventSessionUpdated;

use Storage;

class HopperEventSession {

    protected $hopper;
    protected $hopperfile;
    protected $hopperfileentity;
	
	public $hopper_temporary_name;
    public $hopper_working_name;
    public $hopper_master_name;
    public $hopper_archive_name;
	

    /**
     * @param HopperContract            $hopper
     * @param HopperFileContract        $hopperfile
     */
    public function __construct(
    HopperContract $hopper, HopperFileContract $hopperfile, \App\Services\Hopper\HopperFileEntity $hopperfileentity
    ) {
        $this->hopper = $hopper;
        $this->hopperfile = $hopperfile;
        $this->hopperfileentity = $hopperfileentity;
		
		$this->hopper_temporary_name = env('HOPPER_TEMPORARY_NAME', 'temporary/');
        $this->hopper_working_name = env('HOPPER_WORKING_NAME', 'working/');
        $this->hopper_master_name = env('HOPPER_MASTER_NAME', '1_Master/');
        $this->hopper_archive_name = env('HOPPER_ARCHIVE_NAME', 'ZZ_Archive/');
    }

    /**
     * Creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($data = []) {
        //
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  array $request
     * @return App\Models\Hopper\EventSession
     */
    public function store($data) {
        //
        $eventsession = EventSession::create($data);


        event(new EventSessionUpdated($eventsession->id, 'created', 'Created a new Event Session'));

        return $eventsession;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return App\Models\Hopper\EventSession
     */
    public function show($id) {
        //
    }

    /**
     * Editing the specified resource.
     *
     * @param  int  $id
     * @return App\Models\Hopper\EventSession
     */
    public function edit(EventSession $eventsession) {

        $this->parseDateTimeforEdit($eventsession);
        
        //$this->hopperfile->purgeDupesToArchive();

        $data = [
            'eventsession' => $eventsession,
           // 'History' => $this->hopper->groupedHistory($eventsession->history),
        ];

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $data
     * @param  App\Models\Hopper\EventSession  $eventsession
     * @return App\Models\Hopper\EventSession
     */
    public function update($data, EventSession $eventsession) {
        //Manipulate Dates
        $this->parseDateTimeforStorage($data);

        $eventsession->update($data);

        return $eventsession;
    }

    public function updateCheckInStatus(&$request, &$messagebag, $id = null) {
        switch ($request->action) {
            case 'check_in':
                $request->merge([
					'checked_in' => 'YES',
					'check_in_datetime' => \Carbon\Carbon::now( config('hopper.event_timezone') ),
				]);
                $messagebag->add('checked_in', "<strong class='lead'>Checked In</strong>");
//                event(new EventSessionUpdated($id, 'checked_in', 'Checked in'));
                break;
            case 'check_out':
                $request->merge(['checked_in' => 'NO', 'check_in_datetime' => null]);
                $messagebag->add('checked_in', "<strong class='lead'>Checked Out</strong>");
//                event(new EventSessionUpdated($id, 'checked_out', 'Checked out'));
                break;
            case 'create_visit':
//                $messagebag->add('create_visit', "<strong class='lead'>Visit Created</strong>");
                break;
            case 'update':

                break;
            default:
//                event(new EventSessionUpdated($id, 'update', ''));
                break;
        }
    }

    public function createNewVisit(Request $request, HopperVisit $hoppervisit) {
        
        if (!$request->has('checkin_username')) {
            $request->merge(['checkin_username' => \Auth::user()->name]);
        }		
//        $request->merge(['file_entity_id' => $request->primary_file_entity_id]);
        //$visit = $hoppervisit->store($request->all());
//        event(new EventSessionUpdated($request->event_session_id, 'visit_created', 'Created a new Visit: ' . $visit->id));
		
		
        //If it's a blind update
        if($request->blind_update === "YES"){
             
//			debugbar()->info($request->currentfilename);
//			debugbar()->info($request->filename);
			
			$master_stream = $this->hopperfile->getStream($this->hopper_master_name . $request->currentfilename);
			$new_file_stream = $this->hopperfile->getStream($this->hopper_temporary_name . $request->filename);

			$path = $this->hopperfile->copyTemporaryNewFileToMaster($request->filename, $master_stream);    
//			$this->hopperfile->getStream
			
			//Update Visitor Info
//            $visit->visitors = "(blind update)";
//            $visit->difficulty = "1";
//            $visit->design_notes = "This was a blind update";
//            $visit->design_username = \Auth::user()->name;
			//Save
//            $visit->save();
//			\Log::info('Blind Update Occurred: '.$request->filename);
			//Copy the new master to archive
            //$this->hopperfile->copyMasterToArchive($request->filename, $master_stream);
			
			if($this->hopperfile->copyMasterToArchive($request->filename)){
				//Move the old master to archive
				$this->hopperfile->moveMasterToArchive($request->currentfilename);
			}
			
			//Find the file entity by reference
			//$fileentity = \App\Models\Hopper\FileEntity::find($request->primary_file_entity_id);
			//Update File Entity Referenced
			//$updated_fileentity = $this->hopperfileentity->update($request, $fileentity);
			//$id, $event, $notes = '', $filename = null, $tasks = [], $user = 'Hopper', $request = null
            //event(new \App\Events\Backend\Hopper\FileEntityUpdated($visit->file_entity->id, 'visit_behavior', 'Copied master file '.$visit->file_entity->filename.' to working', $request->filename, ['update_path' => $path]));
			
            //We're done here
            return true;
            
        }
		//If it's not a blind update
		//
        //If there is no updated file
        if($request->currentfilename === $request->filename){
			//Copy the current file in Master to Archive
			//$this->hopperfile->copyMasterToArchive($request->currentfilename);
			//Copy the current file in Master into Working
			$this->hopperfile->copyMasterToWorking($request->currentfilename);
			//Up the version number in Master
            $path = $this->hopperfile->copyMasterToMaster($request->currentfilename, $request->next_version);
            //Up the version number and copy that to Working
            $path = $this->hopperfile->copyMasterToWorking($request->currentfilename, $request->next_version);
			//Use that as working_filename
			$request->merge(['working_filename' => basename($path)]);
			//Copy the new file in Master into Archive
			$this->hopperfile->copyMasterToArchive($request->currentfilename, $request->next_version);
			//Move the old file in Master to Archive
            $this->hopperfile->moveMasterToArchive($request->currentfilename);
			//Find the fileentity by ID
			//$fileentity = \App\Models\Hopper\FileEntity::find($request->primary_file_entity_id);
			//Update
			//$updated_fileentity = $this->hopperfileentity->update($request, $fileentity);
			//Otherwise, if there's a filename and an entity and the currentfilename (old file) does not equal the filename passed in the request
        }elseif($request->filename && $request->currentfilename && ($request->currentfilename !== $request->filename)){
			//Copy the current file in Master to Working
			$this->hopperfile->copyMasterToWorking($request->currentfilename);
            //Copy the current file in Temporary to Master
            $path = $this->hopperfile->copyTemporaryNewFileToMaster($request->filename);
			//Use that as working_filename
			$request->merge(['working_filename' => $request->filename]);
            //Copy the new file in Master into Archive
            $this->hopperfile->copyMasterToArchive($request->filename);
            //Move the old file in Master to Archive
            $this->hopperfile->moveMasterToArchive($request->currentfilename);
            //Move the current file in Temporary to Working
            $this->hopperfile->moveTemporaryNewFileToWorking($request->filename);
			//Find the fileentity by ID
			//$fileentity = \App\Models\Hopper\FileEntity::find($request->primary_file_entity_id);
			//Update
			//$updated_fileentity = $this->hopperfileentity->update($request, $fileentity);
        }
        //$id, $event, $notes = '', $filename = null, $tasks = [], $user = 'Hopper', $request = null
//		if(!empty($visit->file_entity->id)){
//			 event(new \App\Events\Backend\Hopper\FileEntityUpdated($visit->file_entity->id, 'visit_behavior', 'Copied master file '.$visit->file_entity->filename.' to working', $request->filename, ['update_path' => $path]));
//        
//		}
//		
        //Create a visit		
		$visit = $hoppervisit->store($request->all());
		
        return $visit;
    }

    public function createNewFileEntity(Request $request, EventSession $eventsession) {
        
        //Add a New File Entity
        $request->merge(['event_session_id' => $eventsession->id]);
        $fileentity = $this->hopperfileentity->store($request->all());

        event(new EventSessionUpdated($request->id, 'file_entity_created', 'Created a new File Entity: ' . $fileentity->id));

        return $fileentity;
    }

    public function updateNewFileEntity(Request $request, EventSession $eventsession) {
        //Find the file entity by reference
        $fileentity = \App\Models\Hopper\FileEntity::find($request->primary_file_entity_id);
        //Update File Entity Referenced
        $updated_fileentity = $this->hopperfileentity->update($request, $fileentity);
		//$id, $event, $notes = '', $filename = null, $tasks = [], $user = 'Hopper', $request = null
        event(new EventSessionUpdated($request->id, 'file_entity_updated', 'Updated a File Entity: ' . $updated_fileentity->id));

        return $updated_fileentity;
    }
	
	public static function modifyCheckinTime($EventSession){
        if ($EventSession->checked_in === 'YES' && $EventSession->check_in_datetime === NULL) {
            $EventSession->check_in_datetime = \Carbon\Carbon::now(config('hopper.event_timezone'));
        } elseif ($EventSession->checked_in !== 'YES' && $EventSession->check_in_datetime !== NULL) {
            $EventSession->check_in_datetime = NULL;
        }
        return $EventSession;
    }

    public function parseDateTimeforEdit(&$data) {
        if (isset($data->dates_rooms) && is_array($data->dates_rooms)) {
            $new_array = [];
            foreach ($data->dates_rooms as $date_room) {
                $date_room->date = \Carbon\Carbon::parse($date_room->date)->timezone(config('hopper.event_timezone', 'UTC'))->format('m/d/y h:i A');
                $new_array[] = $date_room;
            }
            $data->dates_rooms = $new_array;
        }
    }
    
    public function getDateTimesString($dates_rooms) {
        $dateTimeString = '';
        if (isset($dates_rooms) && is_array($dates_rooms)) {
            
            foreach ($dates_rooms as $key => $date_room) {
                $_date = \Carbon\Carbon::parse($date_room->date)->timezone(config('hopper.event_timezone', 'UTC'))->format('m/d/y h:i A');
                $_room = $date_room->room_name;
                $_room_id = $date_room->room_id;
                $dateTimeString .= $_date . ', ' . $_room . ', ' . $_room_id .';';
            }
           
        }
        return $dateTimeString;
    }
    
    public function setFromDateTimesString($dateTimeString, $delimiter = ';') {
        return self::modifyFromDateTimesString($dateTimeString, $delimiter);
    }

    public function parseDateTimeforStorage(&$data) {
        if (isset($data['dates_rooms']) && is_array($data['dates_rooms'])) {

            foreach ($data['dates_rooms'] as $key => $date_room) {
                $data['dates_rooms'][$key]['date'] = \Carbon\Carbon::parse($date_room['date'])->timezone(config('hopper.event_timezone', 'UTC'))->toIso8601String();
            }
        }
    }
    
    public function parseForExport($EventSessions){
        if($EventSessions->isEmpty()){
            return $EventSessions;
        }
        
        $EventSessionArray = $EventSessions->toArray();
        
        foreach($EventSessionArray as $key => $EventSession){
              
            $EventSessionArray[$key]['dates_rooms'] = $this->getDateTimesString($EventSession['dates_rooms']);
           
            $EventSessionArray[$key]['checked_in'] = (!empty($EventSession['checked_in']) ? "YES" : "NO");
            //Unset History
//            unset($EventSessionArray[$key]['history']);
            //Unset ID
            unset($EventSessionArray[$key]['id']);
                      
        }
        return $EventSessionArray;            
    }
    
    
    public static function modifyFromDateTimesString($dateTimeString, $delimiter = ';') {
        $dates_rooms = [];
        if (isset($dates_rooms) && is_string($dateTimeString)) {
            $dateTimeString = rtrim($dateTimeString, $delimiter);
            $_dates_rooms = explode($delimiter, $dateTimeString);
            //Assumed string is {date},{room_name},{room_id}
            foreach ($_dates_rooms as $key => $date_room) {
                $date_room_obj = new \stdClass;
                $date_room = explode(',', $date_room);
                $date_room_obj->date = \Carbon\Carbon::parse($date_room[0])->timezone(config('hopper.event_timezone', 'UTC'))->toIso8601String();
                $date_room_obj->room_name = trim($date_room[1]);
                $date_room_obj->room_id = trim($date_room[2]);
                $dates_rooms[$key] = $date_room_obj;
            }
//           
        }
//        debugbar()->info($dates_rooms);
        return $dates_rooms;
    }

}
