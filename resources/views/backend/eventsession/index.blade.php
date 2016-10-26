@extends('backend.layouts.app')

@section('title', app_name() .' | '. trans('eventsession.backend.sidebar.title') .' | '. trans('eventsession.backend.sidebar.index'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('eventsession.backend.admin.title') }} &raquo; {{ trans('eventsession.backend.admin.index') }}</small>
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
             {!! $html->table(['class' => 'table responsive table-bordered table-striped', 'width' => '100%' ]) !!}
        </div><!-- /.box-body -->
    </div><!--box box-success-->
	{!! history()->renderType('Visit', 10, true) !!}
@endsection



@section('after-scripts-end')
	@include('includes/partials/pusher')
    {!! $html->scripts() !!}
@endsection
