<?php

namespace App\Http\Controllers\Backend;

ini_set( 'auto_detect_line_endings', true );

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Yajra\Datatables\Html\Builder; // import class on controller
use App\Services\Hopper\HopperEventSession;
use App\Services\Hopper\HopperFileEntity;
use App\Services\Hopper\HopperVisit;
use App\Services\Hopper\HopperStats;
use App\Models\Hopper\EventSession;
use App\Models\Hopper\Visit;
use App\Models\Hopper\FileEntity;
use Input;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon as Carbon;
use App\Services\Hopper\Excel\EventSessionImport;
use App\Services\Hopper\Contracts\HopperFileContract;

class HopperAdminController extends Controller {

	private $messagebag;
	private $hoppereventsession;
	private $hoppervisit;
	private $hopperfileentity;
	private $hopperstats;
	private $hopperfile;
	private $eventsessionimport;

	public function __construct(
	\Illuminate\Support\MessageBag $messagebag, HopperEventSession $hoppereventsession, HopperVisit $hoppervisit,
 HopperFileEntity $hopperfileentity, HopperStats $hopperstats,
 HopperFileContract $hopperfile
//             EventSessionImport $eventsessionimport
	) {
		$this->messagebag			 = $messagebag;
		$this->hoppereventsession	 = $hoppereventsession;
		$this->hoppervisit			 = $hoppervisit;
		$this->hopperfileentity		 = $hopperfileentity;
		$this->hopperstats			 = $hopperstats;
		$this->hopperfile			 = $hopperfile;
//        $this->eventsessionimport = $eventsessionimport;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$data = [ ];

		return view( 'backend.hopper.admin.index', $data );
	}

	public function importEventSessions( Request $request ) {
		$data = [ ];
		return view( 'backend.hopper.admin.import.eventsessions', $data );
	}

	public function importUpload( Request $request, $model ) {
//		$headers = null;
		$results			 = null;
		$required_headers	 = config('hopper.import.required_headers');
		$file				 = $request->file( 'file' );
		
		if ( !$file->isValid() ) {
			return response()->json(
			[
				'success'	 => false,
				'reason'	 => 'Invalid File Upload',
			], 400 );
		}

		$results = \Excel::load($file->getRealPath())->first();

		if (0 === count(array_diff($required_headers, $results->keys()->toArray()))) {
			$uploadedFileName	 = $file->getClientOriginalName(); // Get File Name
			$upload				 = $this->hopperfile->uploadToTemporary( File::get( $file ), $uploadedFileName );
			return response()->json( [
				'success'	 => true,
				'payload'	 => [
					'upload'	 => $upload,
					'headers'	 => $results->keys(),
				],
			], 200 );
		  } else {
			return response()->json('The required columns are not present', 400 );
		  }	

		return response()->json(
		[
			'success'	 => false,
			'reason'	 => 'Invalid File Upload',
		], 400 );
	}
	
	public function processUpload( Request $request, EventSessionImport $eventsessionimport){
		$count = 0;
		$basepath = Storage::disk('hopper')->getDriver()->getAdapter()->getPathPrefix() . '/';
		switch ( $request->model ) {
			case 'eventsessions':
				if ( ! Storage::disk('hopper')->has($request->filename) ) {
					return back()->with( 'flash_error', "There's no file to update from!" );
				}
				
				try {
					$basepath = Storage::disk('hopper')->getDriver()->getAdapter()->getPathPrefix();
					\Excel::filter( 'chunk' )->load( $basepath . $request->filename )->chunk( 250, function($results) {
						foreach ( $results as $row ) {
							if ( !empty( $row[ 'id' ] ) ) {
								$row[ 'id' ] = (int) $row[ 'id' ];
							}
							if ( !empty( $row[ 'dates_rooms' ] ) ) {
								$row[ 'dates_rooms' ] = \App\Services\Hopper\HopperEventSession::modifyFromDateTimesString( $row[ 'dates_rooms' ] );
							}

							$searchParams = [
								'session_id' => $row[ 'session_id' ],
							];
							EventSession::firstOrNew( $searchParams )->fill( $row->all() )->save();
						}
					} );
					$entries = $eventsessionimport->get();
					$count	 = count( $entries );
					return redirect()->route( 'admin.eventsession.index' )
						->with( 'flash_success',  "There were " . $count . " " . $request->model . " that were updated." );
				} catch ( App\Exceptions\GeneralException $e ) {
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_warning', $e->getMessage() );
				}
				break;
			default:
				
				return back()->with( 'flash_error', "There's no file to update from!" );
				break;
		}
		
		return back()->with( 'flash_error', "There's no file to update from!" );
		
	}

	/**
	 * Generate Excel Spreadsheet from the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  $model 
	 * @return \Illuminate\Http\Response
	 */
	public function import( Request $request, EventSessionImport $eventsessionimport, $model ) {
		$count = 0;

		switch ( $model ) {
			case 'event_sessions':
				if ( !file_exists( env( 'HOPPER_STORAGE', storage_path() . '/app' ) . "/import" . '/import.xlsx' ) ) {
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_info', "There's no file to update from!" );
				}
//                
				try {
					\Excel::filter( 'chunk' )->load( env( 'HOPPER_STORAGE', storage_path() . '/app' ) . "/import" . '/import.xlsx' )->chunk( 250, function($results) {
						foreach ( $results as $row ) {
							if ( !empty( $row[ 'id' ] ) ) {
								$row[ 'id' ] = (int) $row[ 'id' ];
							}
							if ( !empty( $row[ 'dates_rooms' ] ) ) {
								$row[ 'dates_rooms' ] = \App\Services\Hopper\HopperEventSession::modifyFromDateTimesString( $row[ 'dates_rooms' ] );
							}

							$searchParams = [
								'session_id' => $row[ 'session_id' ],
							];
							EventSession::firstOrNew( $searchParams )->fill( $row->all() )->save();
						}
					} );
					$entries = $eventsessionimport->get();
					$count	 = count( $entries );
				} catch ( App\Exceptions\GeneralException $e ) {
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_warning', $e->getMessage() );
				}

				break;
			case 'file_entities':

				$count = $this->hopperfileentity->import();

				break;
			default:
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_warning', "I'm not sure what you want me to do." );
				break;
		}

		return redirect()->route( 'backend.hopper.admin.index' )
		->with( 'flash_info', "There were " . $count . " " . $model . " that were updated." );
	}

	/**
	 * Export the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function export( Request $request, $model ) {
		$now = Carbon::now()->format( 'Y-m-d-h-i-s' );
		switch ( $model ) {
			case 'event_sessions':
				try {
					$EventSessionArray = $this->hoppereventsession->parseForExport( EventSession::all() );

//                    return redirect()->route('backend.hopper.admin.index')
//                                    ->with('flash_warning', "Something Happened");

					\Excel::create( 'event_sessions_' . $now, function($excel) use($EventSessionArray) {

						$excel->sheet( 'EventSessions', function($sheet) use($EventSessionArray) {

							$sheet->fromArray( $EventSessionArray );
						} );
					} )->download( 'xlsx' );
				} catch ( Exception $e ) {
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_warning', "Something Happened" );
				}
				break;
			case 'visits':
				try {

					$Visits = $this->hoppervisit->parseForExport( Visit::all() );

					$VisitArray = $this->hopperstats->sessions_with_multiple_visits();

					$VisitArrayOverTime = $this->hopperstats->sessions_with_visitors_over_time( \Carbon\Carbon::createFromDate( 2016, 6, 6 ), \Carbon\Carbon::createFromDate( 2016, 6, 9 ) );

					\Excel::create( 'visits_' . $now, function($excel) use($Visits, $VisitArray, $VisitArrayOverTime) {
						$excel->sheet( 'Visits', function($sheet) use($Visits) {

							$sheet->fromModel( $Visits );
						} );
						$excel->sheet( 'MultipleVisits', function($sheet) use($VisitArray) {
							$sheet->fromArray( $VisitArray );
						} );

						foreach ( $VisitArrayOverTime as $key => $VisitDayOverTime ) {
							$excel->sheet( $key, function($sheet) use($VisitDayOverTime) {
								$sheet->fromArray( $VisitDayOverTime );
							} );
						}
					} )->download( 'xlsx' );
				} catch ( Exception $e ) {
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_warning', "Something Happened" );
				}
				break;
			case 'file_entities':
				try {
					$FileEntities = $this->hopperfileentity->parseForExport( FileEntity::all() );

					\Excel::create( 'visits_' . $now, function($excel) use($FileEntities) {

						$excel->sheet( 'Visits', function($sheet) use($FileEntities) {

							$sheet->fromModel( $FileEntities );
						} );
					} )->download( 'xlsx' );
				} catch ( Exception $e ) {
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_warning', "Something Happened" );
				}
				break;
			case 'combined':
				try {
//                    $EventSessions = Hopper::parseEventSessionforExport(EventSession::all());
					$EventSessionArray	 = $this->hoppereventsession->parseForExport( EventSession::all() );
					$Visits				 = $this->hoppervisit->parseForExport( Visit::all() );
					$FileEntities		 = $this->hopperfileentity->parseForExport( FileEntity::all() );
					\Excel::create( 'combined_' . $now, function($excel) use($EventSessions, $Visits, $FileEntities) {

						$excel->sheet( 'EventSessions', function($sheet) use($EventSessions) {

							$sheet->fromArray( $EventSessions );
						} );
						$excel->sheet( 'Visits', function($sheet) use($Visits) {

							$sheet->fromModel( $Visits );
						} );
						$excel->sheet( 'FileEntites', function($sheet) use($FileEntities) {

							$sheet->fromModel( $FileEntities );
						} );
					} )->download( 'xlsx' );
				} catch ( Exception $e ) {
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_warning', "Something Happened" );
				}
				break;


			default:
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_warning', "I'm not sure what you to do." );
				break;
		}
	}

	/**
	 * Update the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request ) {
		$action = $request->get( 'action' );

		if ( empty( $action ) ) {
			return redirect()->route( 'backend.hopper.admin.index' )
			->with( 'flash_warning', "You didn't give me anything to do!" );
		}

		switch ( $action ) {
			case 'reset-checkin':
				$new_array		 = [ ];
				$EventSessions	 = EventSession::where( 'checked_in', '=', true )->get();
				if ( !$EventSessions->isEmpty() ):
//                    try {
//                        foreach ($EventSessions->chunk(100) as $eventSessionChunk) {
//                            foreach ($eventSessionChunk as $eventSession) {
//                                $eventSession->update(['checked_in' => false]);
//                                $new_array = Drive::matchFields($eventSession, config('drive.field_array_match'));
////                        $drive->update_one_entry($new_array, $eventSession->session_id);
//                            }
//                        }
//                    } catch (Exception $e) {
//                        return redirect()->route('backend.hopper.admin.index')
//                                        ->with('flash_warning', "Something Happened");
//                    }
					if($request->ajax()){
						return response()->json( [
							'success'	 => true,
							'payload'	 => "All sessions checked in are checked out.",
						], 200 );
					}
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_success', "All sessions checked in are checked out." );
					
				else:
					if($request->ajax()){
						return response()->json( [
							'success'	 => true,
							'payload'	 => "There were no sessions that were checked in.",
						], 200 );
					}
					return redirect()->route( 'backend.hopper.admin.index' )
					->with( 'flash_info', "There were no sessions that were checked in." );
				endif;

				break;
			case 'reset-visits':
				Visit::truncate();
				if($request->ajax()){
					return response()->json( [
						'success'	 => true,
						'payload'	 => "You've reset all the Visits",
					], 200 );
				}
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_danger', "You've reset all the Visits" );
				break;
			case 'reset-sessions':
				EventSession::where( 'id', 'like', '%%' )->delete();
				\DB::table( 'event_sessions' )->truncate();
				
				if($request->ajax()){
					return response()->json( [
						'success'	 => true,
						'payload'	 => "You've reset all the Event Sessions",
					], 200 );
				}
				
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_danger', "You've reset all the Event Sessions" );
				break;
			case 'reset-files':
				FileEntity::truncate();
				//EventSession::whereNotNull('session_id')->update(['current_file' => '']);
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_danger', "You've reset all the File Entities" );
				break;
			case 'fill-blanks':
//                 EventSession::whereNotNull('session_id')->update(['current_file' => '']);
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_danger', "You've filled all the blanks" );
				break;
			case 'version-fix':
//                HopperFile::fixBadVersions();
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_danger', "You've updated all the versions" );
				break;
//            case 'create-test':
//                 EventSession::firstOrNew([
//                    'session_id' => 'BB6524',
//                ])->fill([
//						'checked_in' => 'NO',
//						'speakers' => 'Birchall',
//						'onsite_phone' => '',
//						'current_file' => 'BB6524_Birchall_[CS13]_LCC12_SHNS.pptx',
//						'approval_brand' => 'NO',
//						'approval_revrec' => 'NO',
//						'approval_legal' => 'NO',
//						'share_internal' => 'NO',
//						'share_external' => 'NO',
//						'share_recording_internal' => 'NO',
//						'share_recording_external' => 'NO',
//						'dates_rooms' => [],
//						'presentation_owner'  => '',
//						'check_in_datetime' => null,])->save();
//                return redirect()->route('backend.hopper.admin.index')
//                                ->with('flash_danger', "You've added a test Session");
//                break;
//            case 'update-manual':
//                $EventSession = EventSession::where('session_id', '=', 'SL6763')->update(['current_file' => 'SL6763_Benjamin_[IT02A][IT02B][IT02C]_LCC11_SHNS.pptx']);
//                return redirect()->route('backend.hopper.admin.index')
//                                ->with('flash_danger', "Updated");
//                break;
//             case 'import-filenames':
//                $excelfile = 'import-filenames.xlsx';
//                $import_storage = env('HOPPER_STORAGE', storage_path() . '/app') . "/import/" . $excelfile;
//
//                if (!file_exists($import_storage)) {
//                    return redirect()->route('backend.hopper.admin.index')
//                                    ->with('flash_info', "There's no file to update from!");
//                }
//
//                $count = 0;
//                \Excel::filter('chunk')->load($import_storage)->chunk(250, function($results) use (&$count) {
//                    $results->each(function($row) use (&$count) {
//                       debugbar()->info($row['filename']);
//                       $fileparts = explode('_',$row['filename']);
//                       debugbar()->info($fileparts[0]);
//                       $searchParams = [
//                            'session_id' => $fileparts[0],
//                        ];
//                       
//                       $EventSession = EventSession::where('session_id', '=', $fileparts[0])->update(['current_file' => $row['filename']]);
//                       
//                       
//                       
//                       
//                       
//                        $count++;
//                    });
//                });
//                return redirect()->route('backend.hopper.admin.index')
//                                ->with('flash_warning', "I'm not sure what you to do.");
//                break;
			default:
				if($request->ajax()){
					return response()->json( [
						'success'	 => false,
						'payload'	 => "I'm not sure what you to do.",
					], 200 );
				}
				return redirect()->route( 'backend.hopper.admin.index' )
				->with( 'flash_warning', "I'm not sure what you to do." );
				break;
		}
	}

//	public function masterFileUpdate(Request $request){
//			$EventSession = EventSession::where('session_id', '=', $request->session_id)->update(['current_file' => $request->current_file]);
//			return  redirect()->route('backend.hopper.admin.index')
//                                ->with('flash_warning', "Updated");
//			
//	}
}
