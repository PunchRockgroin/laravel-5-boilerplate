<?php

namespace App\Services\Hopper;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Hopper\EventSession;
use App\Services\Hopper\HopperVisit;
use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Contracts\HopperFileContract;
use App\Events\Backend\Hopper\EventSessionUpdated;

class HopperEventSession {

    protected $hopper;
    protected $hopperfile;

    /**
     * @param HopperContract            $hopper
     * @param HopperFileContract        $hopperfile
     */
    public function __construct(
    HopperContract $hopper, HopperFileContract $hopperfile
    ) {
        $this->hopper = $hopper;
        $this->hopperfile = $hopperfile;
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

        $data = [
            'eventsession' => $eventsession,
            'History' => $this->hopper->groupedHistory($eventsession->history),
        ];
        debugbar()->info($data);

        //Is there an file entity attached?
        $file_entity = $eventsession->file_entity;
        if (count($file_entity)) {

            $currentVersion = $this->hopper->getCurrentVersion($file_entity->filename);
            $nextVersion = $currentVersion + 1;
            $data = array_merge($data, [
                'FileEntity' => $file_entity,
                'currentVersion' => $currentVersion,
                'nextVersion' => $nextVersion,
            ]);
        }


//        debugbar()->info($eventsession->history);

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
                $request->merge(['checked_in' => 'YES']);
                $messagebag->add('checked_in', "<strong class='lead'>Checked In</strong>");
                event(new EventSessionUpdated($id, 'checked_in', 'Checked in'));
                break;
            case 'check_out':
                $request->merge(['checked_in' => 'NO']);
                $messagebag->add('checked_in', "<strong class='lead'>Checked Out</strong>");
                event(new EventSessionUpdated($id, 'checked_out', 'Checked out'));
                break;
            case 'create_visit':
//                $messagebag->add('create_visit', "<strong class='lead'>Visit Created</strong>");
                break;
            case 'update':

                break;
            default:
                event(new EventSessionUpdated($id, 'update', ''));
                break;
        }
    }

    public function createNewVisit(Request $request, HopperVisit $hoppervisit) {
        
        if (!$request->has('checkin_username')) {
            $request->merge(['checkin_username' => \Auth::user()->name]);
        }

        $visit = $hoppervisit->store($request->all());
        event(new EventSessionUpdated($request->event_session_id, 'visit_created', 'Created a new Visit: ' . $visit->id));
        
        $path = $this->hopperfile->copyMasterToWorking($visit->file_entity->filename);
        //$id, $event, $notes = '', $filename = null, $tasks = [], $user = 'Hopper', $request = null
        event(new \App\Events\Backend\Hopper\FileEntityUpdated($visit->file_entity->id, 'visit_behavior', 'Moved master file '.$visit->file_entity->filename.' to working', $request->filename, ['update_path' => $path]));

        return $visit;
    }

    public function createNewFileEntity(Request $request, EventSession $eventsession) {
        //Get File Entity Service
        $hopperfileentity = new \App\Services\Hopper\HopperFileEntity();
        //Add a New File Entity
        $request->merge(['event_session_id' => $eventsession->id]);
        $fileentity = $hopperfileentity->store($request->all());

        event(new EventSessionUpdated($request->id, 'file_entity_created', 'Created a new File Entity: ' . $fileentity->id));

        return $fileentity;
    }

    public function updateNewFileEntity(Request $request, EventSession $eventsession) {
        //Get File Entity Service
        $hopperfileentity = new \App\Services\Hopper\HopperFileEntity();
        //Find the file entity by reference

        $fileentity = \App\Models\Hopper\FileEntity::find($request->primary_file_entity_id);
        //Update File Entity Referenced
        $updated_fileentity = $hopperfileentity->update($request, $fileentity);

        event(new EventSessionUpdated($request->id, 'file_entity_updated', 'Updated a File Entity: ' . $updated_fileentity->id));

        return $updated_fileentity;
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
        debugbar()->info($dates_rooms);
        return $dates_rooms;
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
            unset($EventSessionArray[$key]['history']);
            //Unset ID
            unset($EventSessionArray[$key]['id']);
                      
        }
        return $EventSessionArray;            
    }

}
