<?php

namespace App\Services\Hopper;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Hopper\Visit;

use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Contracts\HopperFileContract;


class HopperVisit{
    
    
    protected $hopper;
    
    protected $hopperfile;


    /**
     * @param HopperContract               $hopper
     */
    public function __construct(
        HopperContract $hopper,
        HopperFileContract $hopperfile
    )
    {
        $this->hopper = $hopper;
        $this->hopperfile = $hopperfile;
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
            'visit' => $visit
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
        debugbar()->info($data);
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
        
        
        return $visit;
    }
    
    
    
    public function updateLinkedEventSession($data, Visit $visit){
        
        if(!isset($data['action'])){ return; }
        
        if($data['action'] === 'approve_brand'){
            $visit->event_session->update(['approval_brand' => 'YES']);
        }
        elseif($data['action'] === 'disapprove_brand'){
            $visit->event_session->update(['approval_brand' => 'NO']);
        }
    }
}