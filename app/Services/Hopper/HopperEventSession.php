<?php

namespace App\Services\Hopper;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Hopper\EventSession;
use App\Services\Hopper\HopperVisit;
use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Contracts\HopperFileContract;

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
            'eventsession' => $eventsession
        ];
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

    public function updateCheckInStatus(&$request, &$messagebag) {
        switch ($request->action) {
            case 'check_in':
                $request->merge(['checked_in' => 'YES']);
                $messagebag->add('checked_in', "<strong class='lead'>Checked In</strong>");
                break;
            case 'check_out':
                $request->merge(['checked_in' => 'NO']);
                $messagebag->add('checked_in', "<strong class='lead'>Checked Out</strong>");
                break;
            case 'create_visit':
//                $messagebag->add('create_visit', "<strong class='lead'>Visit Created</strong>");
                break;
            case 'update':

                break;
            default:

                break;
        }
    }

    public function createNewVisit(Request $request) {

        $hoppervisit = new HopperVisit();

        if (!$request->has('checkin_username')) {
            $request->merge(['checkin_username' => \Auth::user()->name]);
        }

        $visit = $hoppervisit->store($request->all());

        return $visit;
    }

    public function createNewFileEntity(Request $request, EventSession $eventsession) {
        //Get File Entity Service
        $hopperfileentity = new \App\Services\Hopper\HopperFileEntity();
        //Add a New File Entity
        $request->merge(['event_session_id' => $eventsession->id]);
        $fileentity = $hopperfileentity->store($request->all());
        return $fileentity;
    }

    public function updateNewFileEntity(Request $request, EventSession $eventsession) {
        //Get File Entity Service
        $hopperfileentity = new \App\Services\Hopper\HopperFileEntity();
        //Find the file entity by reference

        $fileentity = \App\Models\Hopper\FileEntity::find($request->primary_file_entity_id);
        //Update File Entity Referenced
        $updated_fileentity = $hopperfileentity->update($request, $fileentity);

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

    public function parseDateTimeforStorage(&$data) {
        if (isset($data['dates_rooms']) && is_array($data['dates_rooms'])) {
//            debugbar()->info($data['dates_rooms']);
            foreach ($data['dates_rooms'] as $key => $date_room) {
                $data['dates_rooms'][$key]['date'] = \Carbon\Carbon::parse($date_room['date'])->timezone(config('hopper.event_timezone', 'UTC'))->toIso8601String();
            }
        }
    }

}
