<?php

namespace App\Services\Hopper\Contracts;

Interface HopperUserContract
{

    public function users_by_status();
	
    public function toggle_state($id);    
	
    public function get_idle();    
	
    public function sanitizeUserCollection($userCollection);  
 

}