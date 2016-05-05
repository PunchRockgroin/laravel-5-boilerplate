<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $eventsession->session_id or '' }} Event Session Data</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="form-group">
            {!! Form::label('session_id', trans('eventsession.backend.form.session_id.label'), ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('session_id', null,['class' => 'form-control', 'placeholder' => trans('eventsession.backend.form.session_id.placeholder')]) !!}
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
        <div class='clearfix'>
            <div class=" pull-right">
                @include('backend.eventsession.partials.actions')
            </div>
        </div>
    </div><!-- /.box-body -->
</div><!--box-->