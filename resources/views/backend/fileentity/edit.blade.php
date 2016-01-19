@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('fileentity.backend.admin.title') .' | '. trans('fileentity.backend.admin.edit') .' '. $FileEntity->filename)

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('fileentity.backend.admin.title') }} &raquo; {{ trans('fileentity.backend.admin.edit') }} {{ $FileEntity->filename }}</small>
    </h1>
@endsection

@section('content')
    {!! Form::model($FileEntity, ['route' => array('admin.fileentity.update', $FileEntity->id), 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'patch']) !!}
    {!! Form::hidden('behavior', 'edit') !!} 
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
        </div><!-- /.box-body -->
    </div><!--box box-success-->
    @include('backend.fileentity.partials.file_upload')
    @include('backend.fileentity.partials.form')
    {!! Form::close() !!}
    
    @if($FileEntity->history)
        
        @include('backend.fileentity.partials.timeline', $FileEntity->history)
    
    @endif
    
@endsection