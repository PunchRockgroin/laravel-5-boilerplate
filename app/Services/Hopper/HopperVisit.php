<?php

namespace App\Services\Hopper;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Hopper\Visit;

use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Contracts\HopperFileContract;
use App\Services\Hopper\Contracts\HopperUserContract;

use App\Services\Hopper\HopperFileEntity;

use App\Events\Backend\Hopper\EventSessionUpdated;

class HopperVisit{
    
    
    protected $hopper;
    protected $hopperfile;
    protected $hopperuser;


    /**
     * @param HopperContract        $hopper
     * @param HopperFileContract    $hopperfile
     */
    public function __construct(
        HopperContract $hopper,
        HopperFileContract $hopperfile,
		HopperUserContract $hopperuser
    )
    {
        $this->hopper = $hopper;
        $this->hopperfile = $hopperfile;
		$this->hopperuser = $hopperuser;
    }


    /**
     * Creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($data = [])
    {
        //
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  array $request
     * @return App\Models\Hopper\Visit
     */
    public function store($data)
    {
        //
        $visit = Visit::create($data);
        return $visit;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return App\Models\Hopper\Visit
     */
    public function show($id)
    {
        //
    }

    /**
     * Editing the specified resource.
     *
     * @param  int  $id
     * @return App\Models\Hopper\Visit
     */
    public function edit(Visit $visit)
    {

        $data = [
            'visit' => $visit,
			'idleUsers' => null,
			'assignedUser' => null,
        ];
        //Is there an file entity attached?
        $file_entity = $visit->file_entity;
        if(count($file_entity)){
            $currentVersion = $this->hopper->getCurrentVersion($file_entity->filename);
            $nextVersion = $currentVersion + 1;
            $data = array_merge($data, [
                'FileEntity' => $file_entity,
                'currentVersion' => $currentVersion,
                'nextVersion' => $nextVersion,
            ]);
        }
		
		if($visit->assignment_user_id === null){
			$idleUsers = \App\Models\Access\User\User::IdleGraphicOperators()->lists('name', 'id');
			$data['idleUsers'] = $idleUsers;
		}else{
			$data['assignedUser'] = $visit->user;
		}
		
		
        event(new EventSessionUpdated($visit->event_session->id, 'visit_behavior', 'Began Visit'));
//        debugbar()->info($data['idleUsers']);
        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $data
     * @param  App\Models\Hopper\Visit  $visit
     * @return App\Models\Hopper\Visit
     */
    public function update($data, Visit $visit)
    {
        //
        $visit->update($data);
        
        $this->updateLinkedEventSession($data, $visit);
        $this->updateLinkedFileEntity($data, $visit);
        
        
        return $visit;
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  $data
     * @param  App\Models\Hopper\Visit  $visit
     * @return App\Models\Hopper\Visit
     */
    public function blind_update($data, Visit $visit)
    {
        //
        $visit->update($data);
                
        
        return $visit;
    }
    
    
    
    public function updateLinkedEventSession($data, Visit $visit){
        if(!isset($data['action'])){ return; }
        
        switch($data['action']){
            case 'approve_brand':
                $visit->event_session->update(['approval_brand' => 'YES']);
                event(new EventSessionUpdated($visit->event_session->id, 'visit_behavior', 'Approved branding'));
                break;
            case 'disapprove_brand':
                $visit->event_session->update(['approval_brand' => 'NO']);
                event(new EventSessionUpdated($visit->event_session->id, 'visit_behavior', 'Rejected branding'));
                break;
            default:
                //event(new EventSessionUpdated($visit->event_session->id, 'visit_behavior', 'Visit Updated'));
                break;
        }
        
    }
            
    public function updateLinkedFileEntity($data, Visit $visit){
           
          if(isset($data['behavior']) && isset($data['filename']) && isset($data['newfile']) && $data['behavior'] === 'update_visit'){
                
                $path = $this->hopperfile->copyTemporaryNewFileToMaster($data['filename'], true);
                //$id, $event, $notes = '', $filename = null, $tasks = [], $user = 'Hopper', $request = null
                event(new \App\Events\Backend\Hopper\FileEntityUpdated($visit->file_entity->id, 'visit_behavior', 'Moved updated visit file '.$data['filename'].' to master', $data['filename'], ['update_path' => $path]));
                
				$this->hopperfile->copyMasterToArchive($data['filename']);
                event(new \App\Events\Backend\Hopper\FileEntityUpdated($visit->file_entity->id, 'visit_behavior', 'Copied updated visit file '.$data['filename'].' to archive', $data['filename']));
                
          }
        
//        if(!isset($data['action'])){ return; }
        
//        switch($data['action']){
//            case 'approve_brand':
//                $visit->event_session->update(['approval_brand' => 'YES']);
//                event(new EventSessionUpdated($visit->event_session->id, 'visit_behavior', 'Approved branding'));
//                break;
//            case 'disapprove_brand':
//                $visit->event_session->update(['approval_brand' => 'NO']);
//                event(new EventSessionUpdated($visit->event_session->id, 'visit_behavior', 'Rejected branding'));
//                break;
//            default:
//                event(new EventSessionUpdated($visit->event_session->id, 'visit_behavior', 'Visit Updated'));
//                break;
//        }
        
    }
    
    public function parseForExport($Visits){
        if($Visits->isEmpty()){
            return $Visits;
        }
         
        foreach($Visits as $key => $Visit){
            unset($Visits[$key]->id);
            unset($Visits[$key]->history);       
        }
        return $Visits;            
    }
}