<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Check in options</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
		{!! Html::checkboxswitch(
			'visitor_type',
			'Is the visitor physically present?',
			'YES',
			[ 'data-on-color'=>'success', 'data-off-color'=>'warning',]
			)
		!!}
		{!! Html::checkboxswitch(
			'blind_update',
			'If there is an updated file, are we bypassing review/additional changes by a Graphic Operator?',
			'NO',
			[ 'data-on-color'=>'warning', 'data-off-color'=>'default',],
			null,
			'YES',
			'NO',
			'This is also known as a "blind update"'
			)													
		!!}
		@if( config('hopper.print.enable') )
		{!! Html::checkboxswitch(
			'print_form',
			'Print a check-in form?',
			'YES',
			[ 'data-on-color'=>'success', 'data-off-color'=>'warning',]
			)													
		!!}
		@endif
		
	</div>
	<div class="box-footer">
		<div class='clearfix'>
            <div class=" pull-right">
                @include('backend.eventsession.partials.actions')
            </div>
        </div>
	</div>
</div>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $eventsession->session_id or '' }} Event Session Data</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="form-group">
            {!! Form::label('session_id', trans('eventsession.backend.form.session_id.label'), ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('session_id', null,['class' => 'form-control', 'placeholder' => trans('eventsession.backend.form.session_id.placeholder'), 'readonly'=>'readonly']) !!}
                <div class="help-block">{{ trans('eventsession.backend.form.session_id.help_block') }}</div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('speakers', trans('eventsession.backend.form.speakers.label'), ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('speakers', null,['class' => 'form-control', 'placeholder' => trans('eventsession.backend.form.speakers.placeholder')]) !!}
                <div class="help-block">{{ trans('eventsession.backend.form.speakers.help_block') }}</div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('onsite_phone', trans('eventsession.backend.form.onsite_phone.label'), ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('onsite_phone', null,['class' => 'form-control', 'placeholder' => trans('eventsession.backend.form.onsite_phone.placeholder')]) !!}
                <div class="help-block">{{ trans('eventsession.backend.form.onsite_phone.help_block') }}</div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('presentation_owner', trans('eventsession.backend.form.presentation_owner.label'), ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('presentation_owner', null,['class' => 'form-control', 'placeholder' => trans('eventsession.backend.form.presentation_owner.placeholder')]) !!}
                <div class="help-block">{{ trans('eventsession.backend.form.presentation_owner.help_block') }}</div>
            </div>
        </div>
		
		@if(config('hopper.use_dates', false))
        <div class="repeater">
            <div data-repeater-list="dates_rooms" class="row">
                @if(!empty($eventsession) && !empty($eventsession->dates_rooms))
                    @each('backend.eventsession.partials.date_room_fields', $eventsession->dates_rooms, 'date_room')
                @else
                    @include('backend.eventsession.partials.date_room_fields', ['key' => 0 ])
                @endif
            </div> 
            <a data-repeater-create class='btn btn-success' >Add</a>
        </div>
		@endif
    </div><!-- /.box-body -->
	<div class="box-footer">
		<div class='clearfix'>
            <div class=" pull-right">
                @include('backend.eventsession.partials.actions')
            </div>
        </div>
	</div>
</div><!--box-->