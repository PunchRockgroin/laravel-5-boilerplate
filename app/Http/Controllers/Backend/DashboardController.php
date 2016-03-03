<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Hopper\Contracts\HopperContract as Hopper;
/**
 * Class DashboardController
 * @package App\Http\Controllers\Backend
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index(Hopper $hopper)
    {
        $data = [];
//        $filtered = [];
//        $grouped = [];
//               
//        $elusers = [];
//        
////        $admins = \App\Models\Access\Role\Role::all();
//          $users = \App\Models\Access\User\User::all();
//          
////        debugbar()->info($users->all());
//        
////        
//          
//        $users->transform(function ($item, $key) {
//            $value = \Cache::get('heartbeat-'.md5($item['email']));
//            if($value){
//                $item['statusclass'] = 'yellow';
//                $item['heartbeat'] = json_decode($value, TRUE);
//            }else{
//                $item['statusclass'] = 'gray';
//                $item['heartbeat'] = ['route'=>'offline'];
//            }
//            $item['roles'] = $item->roles;
//            return $item;
//        });  
//        //debugbar()->info($users);
//        
//        $grouped = $users->groupBy(function ($item, $key) {
//            return $item['heartbeat']['route'];
//        });
//        
//          debugbar()->info($grouped);
//       
//        
////        $filtered = $grouped->only(['admin.filentity.edit']);
//        
//        
//        
////        $filtered = $grouped->only('admin.fileentity.edit');
//        if(isset($grouped['admin.visit.edit'])){
//            $filtered = $grouped['admin.visit.edit'];
//            unset($grouped['admin.visit.edit']);
//        }
//        
//        
////        $filtered->transform(function ($item, $key) {
//////            $item['blah'] = 'foo';
////            return $item;
////        });
//             
//        
//        
//         debugbar()->info($filtered);
//          debugbar()->info($grouped);
//        
//        $data['inVisit'] = $filtered;
//        $data['other'] = $grouped;
//        

       
        
        
        return view('backend.dashboard', $data);
    }
    
    public function data(\Illuminate\Http\Request $request){
        $filtered = [];
        $grouped = [];
        $users = \App\Models\Access\User\User::all();

        $users->transform(function ($item, $key) {
            $value = \Cache::get('heartbeat-'.md5($item['email']));
            if($value){
                $item['statusclass'] = 'yellow';
                $item['heartbeat'] = json_decode($value, TRUE);
            }else{
                $item['statusclass'] = 'gray';
                $item['heartbeat'] = ['route'=>'offline'];
            }
            $item['roles'] = $item->roles;
            return $item;
        });  
        //debugbar()->info($users);
        
        $data['groups'] = $users->groupBy(function ($item, $key) {
            return $item['heartbeat']['route'];
        });

        if(isset($data['groups']['admin.visit.edit'])){
            $filtered = $data['groups']['admin.visit.edit'];
            unset($data['groups']['admin.visit.edit']);
            $filtered->transform(function ($item, $key) {
                $visit = \App\Models\Hopper\Visit::find($item['heartbeat']['parameters'][2]);
                if($visit){
                    $item['visit'] = $visit ;
                }
                return $item;
            });
        }
        

        $data['inVisit'] = $filtered;
        
        return response()->json(['message' => 'ok', 'payload' => $data]);
        
    }
}