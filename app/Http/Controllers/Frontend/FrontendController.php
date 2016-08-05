<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Artisan;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class FrontendController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        javascript()->put([
            'test' => 'it works!',
        ]);

        return view('frontend.home');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function macros()
    {
        return view('frontend.macros');
    }
	
	
	public function installer(){
		
		if(! \Schema::hasTable('user') ):
			
		$exitCode = Artisan::call('migrate');
		
		$exitCode .= Artisan::call('db:seed');
		
		return redirect(route('frontend.index'))->with('flash_success', 'Installed');
		
		else:
			return redirect(route('frontend.index'))->with('flash_danger', 'This is not a fresh install');
		endif;
		
	}
	
}
