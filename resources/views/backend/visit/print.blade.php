@extends('backend.layouts.print')

@section ('title',  $visit->id)

@section('content')
        @include('backend.visit.partials.checkinsheet') 
@endsection