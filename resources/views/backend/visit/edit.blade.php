@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('visit.backend.admin.title') .' | '. trans('visit.backend.admin.edit')) 

@section('page-header')
<h1>
    {!! app_name() !!}
    <small>{{ trans('visit.backend.admin.title') }} &raquo; Visit ID {{ $visit->id }} for {{ $visit->event_session->session_id }} &raquo; {{ trans('visit.backend.admin.edit') }}</small>
</h1>
@endsection

@section('content')

{!! Form::model($visit, ['route' => array('admin.visit.update', $visit->id), 'role' => 'form', 'method' => 'patch']) !!}
{!! Form::hidden('behavior', 'update_visit') !!} 

<div class="row">
    <div class="col-sm-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-file-powerpoint-o"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Filename</span>
                <span class="info-box-number">{!! $visit->file_entity->filename !!}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>

</div>
<div class="row">

</div>
@include('backend.visit.partials.file_upload')

<div class="row">

    <div class="col-sm-12 col-md-5 col-md-push-7">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Assignment</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					@if($visit->assignment_user_id === null)
					<div><strong>Unassigned</strong></div>
						@if($idleUsers)
							{{ Form::select('assignment_user_id', $idleUsers, null, ['placeholder' => 'Choose a Graphic Operator']) }}
						@endif
					@else
						@if($assignedUser)
						<div class="alert alert-info">Assigned to: <strong>{{ $assignedUser['name'] }}</strong></div>
						@endif
					@endif
				</div><!-- /.box-body -->
				@if($visit->assignment_user_id === null)
				<div class="box-footer">
					@include('backend.visit.partials.actions')
				</div>
				@endif
			</div><!-- /.box -->
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Session ID</span>
                    <span class="info-box-number">{!! $visit->event_session->session_id !!}</span>
                </div>
                <!-- /.info-box-content -->
            </div>

			@if(config('hopper.use_dates', false))
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-clock-o"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Room/Date/Time</span>
                    @inject('Hopper', 'App\Services\Hopper\Contracts\HopperContract')
                    @foreach($visit->event_session->dates_rooms as $date_room)
                    <div><span class="info-box-number"><span class="small">
                                {!! $date_room->room_name !!}  <span class="small">({!! $date_room->room_id !!})</span>
                                {!! $Hopper->parseDateTimeForDisplay($date_room->date) !!} 
                                </span></span></div>
                        
                    @endforeach
                </div>
                <!-- /.info-box-content -->
            </div>
			@endif
			
            <div class="info-box">
                <span class="info-box-icon bg-{!! ($visit->event_session->approval_brand === 'YES' ? 'green' : 'red') !!}"><i class="fa fa-check-circle"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Branding Status</span>
                    <span class="info-box-number">{!! ($visit->event_session->approval_brand === 'YES' ? 'APPROVED' : 'DISAPPROVED') !!}</span>
                </div>
                <!-- /.info-box-content -->
            </div>

    </div>
    <div class='col-sm-12 col-md-7 col-md-pull-5'>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">This Visit</h3>
            </div><!-- /.box-header -->

            <div class="box-body">
                @include('backend.visit.partials.form')                  
            </div><!-- /.box-body -->
            <div class="box-footer">
                @include('backend.visit.partials.actions')
            </div>
        </div><!-- /.box -->
    </div>
</div>

{!! Form::close() !!}
@endsection

@push('after-scripts-end')
<div style="" class="dz-overtop">
    <div>
        <i class="fa fa-5x fa-download"></i>
        <div class='h1'>Drop anywhere</div>
    </div>
</div>
@include('includes/partials/pusher')
<script>
	pusher.subscribe("presence-test_channel");
</script>
@endpush