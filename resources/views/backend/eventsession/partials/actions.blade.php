@if(isset($eventsession) && !$eventsession->checkedInBoolean())
{!!  Form::button('Check In', ['type'=>'submit', 'name' => 'action', 'value'=>'check_in', 'class' => 'btn btn-success']) !!}
@endif
@if(isset($eventsession) && $eventsession->checkedInBoolean())
{!!  Form::button('Create New or Update Visit', ['type'=>'submit', 'name' => 'action', 'value'=>'create_visit', 'class' => 'btn btn-primary']) !!}
@endif

@if(isset($eventsession) && $eventsession->checkedInBoolean())
{!!  Form::button('Check Out', ['type'=>'submit', 'name' => 'action', 'value'=>'check_out', 'class' => 'btn btn-warning']) !!}
@endif

