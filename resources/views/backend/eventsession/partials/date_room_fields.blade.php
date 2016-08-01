<div data-repeater-item>
    <div class="form-group col-sm-3">
        {!! Form::label('dates_rooms['.$key.'][date]', trans('eventsession.backend.form.dates_rooms_date.label'), ['class' => 'control-label']) !!}
        <div class="">
            {!! Form::text('dates_rooms['.$key.'][date]', null, ['class' => 'form-control date-range-picker', 'placeholder' => trans('eventsession.backend.form.dates_rooms_date.placeholder')]) !!}
        </div>
    </div>
    <div class="form-group col-sm-3">
        {!! Form::label('dates_rooms['.$key.'][room_name]', trans('eventsession.backend.form.dates_rooms_room_name.label'), ['class' => 'control-label']) !!}
        <div class="">
            {!! Form::text('dates_rooms['.$key.'][room_name]', null, ['class' => 'form-control', 'placeholder' => trans('eventsession.backend.form.dates_rooms_room_name.placeholder')]) !!}
        </div>
    </div>
    <div class="form-group col-sm-3">
        {!! Form::label('dates_rooms['.$key.'][room_id]', trans('eventsession.backend.form.dates_rooms_room_id.label'), ['class' => 'control-label']) !!}
        <div class="">
            {!! Form::text('dates_rooms['.$key.'][room_id]', null, ['class' => 'form-control', 'placeholder' => trans('eventsession.backend.form.dates_rooms_room_id.placeholder')]) !!}
        </div>
    </div>
    <div class="form-group col-sm-3">
        {!! Form::label('dates_rooms_ops', 'Operations', ['class' => 'control-label']) !!}
        <div class="">
            <a data-repeater-delete class='btn btn-danger' >Delete</a>
        </div>
    </div>
</div>    