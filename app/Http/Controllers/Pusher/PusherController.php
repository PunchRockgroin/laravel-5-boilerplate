<?php

namespace App\Http\Controllers\Pusher;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Vinkla\Pusher\PusherManager;

/**
 * Class PusherController
 * @package App\Http\Controllers\Pusher
 */
class PusherController extends Controller
{
	
	private $pusher;
    
    public function __construct(PusherManager $pusher)
    {
		$this->pusher = $pusher;
    }
	
	
    
	public function doAuth(Request $request)
    {
		
		$presence_data = [
			'name' => auth()->user()->name,
			'email' => auth()->user()->email,
		];
		
		$value = \Cache::get('heartbeat-'.md5(auth()->user()->email));
		if($value){
			$presence_data['statusclass'] = 'yellow';
			$presence_data['heartbeat'] = json_decode($value, TRUE);
		}else{
			$presence_data['statusclass'] = 'gray';
			$presence_data['heartbeat'] = ['route'=>'offline'];
		}
		
		$auth = json_decode($this->pusher->presence_auth($request->channel_name, $request->socket_id, auth()->user()->id, $presence_data));	
		
        return response()->json($auth, 200);
    }
}