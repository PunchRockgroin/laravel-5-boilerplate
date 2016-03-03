<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Vinkla\Pusher\Facades\Pusher;

class HeartbeatController extends Controller
{
    
    
    public function index(Request $request){
        
        
        
//        auth()->
        
//        Cache::forever('user-', 'value');
        
        //Pusher::trigger('hopper-channel', 'heartbeat', ['message' => $request->all()]);
        
        return response()->json(['status'=>'ok']);
        
    }
    
    
    public function status(Request $request){
        
        return response()->json(['message' => 'Abigail', 'state' => 'CA']);
        
    }
}
