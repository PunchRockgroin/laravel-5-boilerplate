@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('eventsession.backend.admin.title') .' | '. trans('eventsession.backend.admin.create'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('eventsession.backend.admin.title') }} &raquo; {{ trans('eventsession.backend.admin.create') }}</small>
    </h1>
@endsection

@section('content')
    
    {!! Form::open(['route' => 'admin.eventsession.store', 'class' => '', 'role' => 'form']) !!}    
    {!! Form::hidden('behavior', 'create_eventsession') !!}
    
    
    @include('backend.eventsession.partials.form')   
    {!!  Form::button('Create New Event Session', ['type'=>'submit', 'name' => 'action', 'value'=>'create_eventsession', 'class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}
@endsection