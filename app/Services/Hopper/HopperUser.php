<?php

namespace App\Services\Hopper;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Access\User\User;

use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Contracts\HopperUserContract;

use Vinkla\Pusher\Facades\Pusher;

class HopperUser implements HopperUserContract{
    
    
    protected $hopper;
    protected $hopperfile;


    /**
     * @param HopperContract        $hopper
     */
    public function __construct(
//        HopperContract $hopper,
//        HopperFileContract $hopperfile
    )
    {
//        $this->hopper = $hopper;
//        $this->hopperfile = $hopperfile;
    }
    
    /**
     * Get all users by status.
     *
     * @return App\Models\Hopper\User
     */
    public function users_by_status()
    {
		
		$users = User::with('assignments')
					->with('visitCount')
					->with('roles')
					->whereHas('roles', function ($query) {
						$query->where('name', '=', 'Graphic Operator');
					})
					->get();
		
		
		$users = $users->each(function ($item, $key) {
			$item = $this->parseUser($item);
		});
		
		$users->except(['confirmation_code', 'confirmed', 'password', 'remember_token']);
//		debugbar()->info($users);
		
        return $users;
    }
	
    /**
     * Toggle the user's state.
     *
     * @param  $id
     * @return App\Models\Hopper\Visit
     */
    public function toggle_state($id)
    {
		$user = User::find($id);
		
		if($user->state === 'active'){
			$user->state = 'idle';
		}else{
			$user->state = 'active';
		}
		
		$user->save();
		
		$user = $this->parseUser($user);
		
        return $user;
    }
	
	
	public function get_idle(){
		$idle_users = User::IdleGraphicOperators();
		return $idle_users;
	}
	
	/**
     * Sanitizes User Collection removing unneeded fields
     *
     * @param  $userCollection
     * @return Collection
     */
	public function sanitizeUserCollection($userCollection){
		return $userCollection->except( 'history', 'confirmation_code','status' ,'confirmed');
	}
	
	
	private function parseUser($user){
		if( ! $user->idle ){
			$user->statusclass = 'green';
		}else{
			$user->statusclass = 'yellow';
		}
		$user->gravatar = \Gravatar::get($user->email);
		
		
		$user->uid = md5($user->email);
		
		return $user;
	}
	

}