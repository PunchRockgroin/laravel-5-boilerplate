@extends('backend.layouts.print')

@section ('title',  $visit->session_id . '_' . str_replace(' ', '_', $visit->created_at))

@section('content')
        @include('backend.visit.partials.checkinsheet') 
@endsection