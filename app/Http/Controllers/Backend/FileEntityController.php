<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Hopper\FileEntity;
use Yajra\Datatables\Datatables;
use App\Services\Hopper\Contracts\HopperContract as Hopper;
use App\Services\Hopper\HopperFile;

class FileEntityController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $fileEntities = FileEntity::all();
        
        debugbar()->info($fileEntities);
        
        $data = [];
        return view('backend.fileentity.index', $data);
    }
    
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        
        return Datatables::of(FileEntity::select('*'))
                ->addColumn('operations',
                    '<a class="btn btn-primary btn-xs" href="{{ route( \'admin.fileentity.edit\', array( $id )) }}">Edit</a> '
                  . '<a class="btn btn-info btn-xs" href="{{ route( \'admin.fileentity.show\', array( $id )) }}">Show</a> '
                )
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $data = [];
        
        return view('backend.fileentity.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HopperFile $hopperfile) {
        //
        
        debugbar()->info($request->all());
        
        $this->validate($request, [
            'newfile' => 'required',
            'filename' => 'required',
        ],
        [
            'newfile.required' => 'A file is required to create a new file',
            'filename.required' => 'A filename is required to create a new file'
        ]);

        
            
        $hopperfile->copyTemporaryNewFileToMaster($request->filename);
        
        $FileEntity = FileEntity::create($request->all());
        
        event(new \App\Events\Backend\Hopper\FileEntityUpdated($id, 'create', 'Created', $FileEntity->filename));
        
        return redirect()->back()->withFlashSuccess('File '.$FileEntity->filename.' Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $data = [];
        return view('backend.fileentity.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Hopper $hopper, HopperFile $hopperfile) {

        $FileEntity = FileEntity::findOrFail($id);
        
        $currentVersion = $hopperfile->getCurrentVersion($FileEntity->filename);
        $nextVersion = $currentVersion + 1;
        
//        $FileHistory = collect($FileEntity->history);
//        $GroupedFileHistory = $FileHistory->groupBy(function($item)
//        {
//          return \Carbon\Carbon::parse($item['timestamp']['date'])->format('d-M-y');   
//        });
        
        $GroupedFileHistory = $hopper->groupedHistory($FileEntity->history);
        debugbar()->info($GroupedFileHistory);
        
        $data = [
            'FileEntity' => $FileEntity,
            'currentVersion' => $currentVersion,
            'nextVersion' => $nextVersion,
            'GroupedFileHistory' => $GroupedFileHistory,
        ];
        return view('backend.fileentity.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, HopperFile $hopperfile) {
        debugbar()->info($request->all());
        
//        $this->validate($request, [
//            'newfile' => 'required',
//        ],
//        [
//            'newfile.required' => 'A file is required to create a new file'
//        ]);

        $FileEntity = FileEntity::findOrFail($id);

//        $request->history[] = $History;
        $FileEntity->update($request->all());
        
        event(new \App\Events\Backend\Hopper\FileEntityUpdated($id, 'update', 'Updated'));
        
        if($request->currentfilename !== $request->filename){
            $hopperfile->copyTemporaryNewFileToMaster($request->filename);
            event(new \App\Events\Backend\Hopper\FileEntityUpdated($id, 'copy', 'Moved to master', null, $request->filename));
            $hopperfile->moveMasterToArchive($request->currentfilename);
            event(new \App\Events\Backend\Hopper\FileEntityUpdated($id, 'move', 'Moved to archive', null, $request->currentfilename));
        }
        
        
        
        
        
        
        
        return redirect()->back()->with('flash_info','File '.$FileEntity->filename.' Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
        return redirect()->with('flash_danger','File Deleted');
    }

    public function upload(Request $request, HopperFile $hopperFile) {
//        \Debugbar::disable();
        
        debugbar()->info($request->all());
        
        $dropboxData = [];
        $file = $request->file('file');
        if (!$file->isValid()) {
            return response()->json(
                            [
                        'success' => false,
                        'reason' => 'Invalid File Upload',
                            ], 400);
        }

        $validate = $hopperFile->validateFile($request);
        if ($validate instanceof Illuminate\Support\MessageBag) {
            return response()->json($validate->first(), 400);
        }

        $extension = $file->getClientOriginalExtension(); // getting file extension




        $uploadedFileName = $file->getClientOriginalName();
        if (!empty($request->filename)) {
            $newFileName = $request->filename;
//            \Debugbar::info($request->filename);
        } else {
            $newFileName = $uploadedFileName;
        }

        if (filter_var($request->currentFileName, FILTER_VALIDATE_BOOLEAN) && filter_var($request->next_version, FILTER_VALIDATE_BOOLEAN)){
            $newFileName = $hopperFile->renameFileVersion($request->currentFileName, $request->next_version, $file->getClientOriginalExtension());
        }
        
       
          
        $upload = $hopperFile->uploadToTemporary(File::get($file), $newFileName);
        
        if ($upload instanceof \Exception) {
            return response()->json([
                        'success' => false,
                        'message' => $upload->getMessage()
//                        'oldfilename' => $request->currentFileName,
//                        'sessionID' => $request->sessionID,
                            ], 400);
        } else {
//            Event::fire(new \App\Events\Backend\Hopper\FileUploaded($request->except('file'), $newFileName));
            
            return response()->json([
                        'success' => true,
//                        'oldFileName' => $request->currentFileName,
                        'newFileName' => $newFileName,
                        'metadata' => $upload
//                        'dropboxData' => $dropboxData,
//                        'sessionID' => $request->sessionID,
                            ], 200);
        }
    }

}
