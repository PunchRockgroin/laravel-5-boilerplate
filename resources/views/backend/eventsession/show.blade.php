@extends('backend.layouts.app')

@section('title', app_name() .' | '. trans('eventsession.backend.admin.title') .' | '. trans('eventsession.backend.admin.show'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('eventsession.backend.admin.title') }} &raquo; {{ trans('eventsession.backend.admin.show') }}</small>
    </h1>
@endsection

@section('content')

@endsection