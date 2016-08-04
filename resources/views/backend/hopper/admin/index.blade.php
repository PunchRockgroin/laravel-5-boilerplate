@extends('backend.layouts.master')

@section ('title',  app_name() . ' | Administration' )

@section('page-header')
    <h1>
        {{ app_name() }}
        <small>Administration</small>
    </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{!!route('admin.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">Here</li>
@endsection

@section('content')
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ app_name() }} Administration</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            {!! Form::open(array('route' => 'backend.hopper.admin.update', 'role'=>'form', 'id'=>'hopperAdminForm')) !!}
            
            
            <a href="{!! route('backend.hopper.admin.export', ['model'=> 'event_sessions']) !!}" class="btn btn-default"><i class="fa fa-file-excel-o"></i> <span class="hidden-xs hidden-sm">Download </span>Event Sessions</a>
            <a href="{!! route('backend.hopper.admin.export', ['model'=> 'visits']) !!}" class="btn btn-default"><i class="fa fa-file-excel-o"></i> <span class="hidden-xs  hidden-sm">Download </span>Visits</a>
            <a href="{!! route('backend.hopper.admin.export', ['model'=> 'file_entities']) !!}" class="btn btn-default"><i class="fa fa-file-excel-o"></i> <span class="hidden-xs  hidden-sm">Download </span>File Entities</a>
            <a href="{!! route('backend.hopper.admin.export', ['model'=> 'combined']) !!}" class="btn btn-default"><i class="fa fa-file-excel-o"></i> <span class="hidden-xs  hidden-sm">Download </span>Complete Status</a>
            

            {!! Form::close() !!}
        </div><!-- /.box-body -->
      </div><!-- /.box -->
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Constructive</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <a href="{!! route('backend.hopper.admin.import.eventsessions') !!}" class="btn btn-default"><i class="fa fa-file-excel-o"></i> <span class="hidden-xs  hidden-sm">Import </span>Event Sessions From File</a>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Destructive</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <a href="{!! route('backend.hopper.admin.update', ['action'=> 'reset-checkin']) !!}" class="btn btn-default destructive-btn"><i class="fa fa-table"></i> Reset Check-ins</a>
            <a href="{!! route('backend.hopper.admin.update', ['action'=> 'reset-sessions']) !!}" class="btn btn-default destructive-btn"><i class="fa fa-table"></i> Reset Sessions</a>
            <a href="{!! route('backend.hopper.admin.update', ['action'=> 'reset-visits']) !!}" class="btn btn-default destructive-btn"><i class="fa fa-table"></i> Reset Visits</a>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
@endsection