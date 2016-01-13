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
        $data['boom'] = $hopper->blastOff();
        
        return view('backend.dashboard', $data);
    }
}