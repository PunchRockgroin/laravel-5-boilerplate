<?php

namespace App\Services\Hopper;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Access\User\User;

use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Contracts\HopperFileContract;

class HopperUser{
    
    
    protected $hopper;
    protected $hopperfile;


    /**
     * @param HopperContract        $hopper
     * @param HopperFileContract    $hopperfile
     */
//    public function __construct(
//        HopperContract $hopper,
//        HopperFileContract $hopperfile
//    )
//    {
//        $this->hopper = $hopper;
//        $this->hopperfile = $hopperfile;
//    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  $data
     * @param  App\Models\Hopper\Visit  $visit
     * @return App\Models\Hopper\Visit
     */
    public function users_by_status()
    {
        //
		
//		$users = User::all();
		
		
		$users = User::getGraphicOperatorStatus();
		
		debugbar()->info($users);
		
		
        return $users;
    }

}