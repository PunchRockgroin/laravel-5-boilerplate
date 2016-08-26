@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('eventsession.backend.admin.title') .' | '. trans('eventsession.backend.admin.edit'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('eventsession.backend.admin.title') }} &raquo; {{ $eventsession->session_id }} &raquo; {{ trans('eventsession.backend.admin.edit') }}</small>
    </h1>
@endsection

@section('content')
    {!! Form::model($eventsession, ['route' => array('admin.eventsession.update', $eventsession->session_id), 'role' => 'form', 'method' => 'patch']) !!}
    {!! Form::hidden('behavior', 'update_eventsession') !!} 
	
	@include('backend.eventsession.partials.file_entity')

    @include('backend.eventsession.partials.form')   
    
    {!! Form::close() !!}
    
    @if(isset($History))
        @include('backend.eventsession.partials.timeline')    
    @endif
@endsection