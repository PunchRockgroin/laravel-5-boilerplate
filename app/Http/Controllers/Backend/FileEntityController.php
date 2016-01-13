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
use App\Services\Hopper\Contracts\HopperContract as Hopper;

use App\Services\Hopper\HopperFile;

class FileEntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        return view('backend.fileentity.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        return view('backend.fileentity.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = [];
        return view('backend.fileentity.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [];
        return view('backend.fileentity.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
    public function upload(Request $request, HopperFile $hopperFile) {
//        \Debugbar::disable();
        $dropboxData = [];
        $file = $request->file('file');
        if (!$file->isValid()) {
            return response()->json([
                        'success' => false,
                        'reason' => 'Invalid File Upload',
//                        'oldfilename' => $request->currentFileName,
//                        'sessionID' => $request->sessionID,
                            ], 400);
        }
                
        $rules = array(
            'file' => 'mimes:'.config('hopper.checkin_upload_mimes'),
        );

        $messages = [
            'mimes' => 'Invalid file type or corrupt file',
        ];

        $validation = \Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();
            return response()->json($errors->first(), 400);
        }
        
        $extension = $file->getClientOriginalExtension(); // getting file extension
        //$currentFileName = $request->currentFileName;
                
        $uploadedFileName = $file->getClientOriginalName();
        $newFileName = $uploadedFileName;
        //$newFileName = $hopperFile->renameFileVersion($currentFileName, $request->nextVersion, $file->getClientOriginalExtension());
        
        //$upload_success = true;       
        $upload_success = $hopperFile->uploadToTemporary(File::get($file), $newFileName);
		debugbar()->info($upload_success);
        if ($upload_success) {
           // Event::fire(new \App\Events\Backend\Hopper\FileUploaded($request->except('file'), $newFileName));
            //Event::fire(new \App\Events\Backend\Hopper\FileUploaded($request->except('file'), $newFileName));
            
            return response()->json([
                        'success' => true,
//                        'oldFileName' => $request->currentFileName,
                        'newFileName' => $newFileName,
//                        'dropboxData' => $dropboxData,
//                        'sessionID' => $request->sessionID,
                            ], 200);
        } else {
            return response()->json([
                        'success' => false,
//                        'oldfilename' => $request->currentFileName,
//                        'sessionID' => $request->sessionID,
                            ], 400);
        }
    }
}
