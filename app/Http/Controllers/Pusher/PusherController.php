<?php

namespace App\Http\Controllers\Pusher;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Vinkla\Pusher\PusherManager;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
			'originator' => $request->get('originator'),
			'target' => $request->get('target'),
			'client_version' => config('hopper.client_version'),
		];
		
		
		$auth = json_decode($this->pusher->presence_auth($request->channel_name, $request->socket_id, auth()->user()->id.'-server', $presence_data));	
		
        return response()->json($auth, 200);
    }
	
	public function doAuthJsonP(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
		$presence_data = [
			'name' => $user->name,
			'email' => $user->email,
			'client_version' => config('hopper.client_version'),
		];
		
		$auth = json_decode($this->pusher->presence_auth($request->channel_name, $request->socket_id, $user->id, $presence_data));	
		
        return response()->json($auth, 200)->withCallback($request->input('callback'));
    }
}