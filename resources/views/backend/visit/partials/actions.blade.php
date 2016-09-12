@if($visit->event_session->approval_brand !== 'YES')
{!!  Form::button('Approve', ['type'=>'submit', 'name' => 'action', 'value'=>'approve_brand', 'id' => "visitUpdateBTN", 'class' => 'btn btn-lg btn-success']) !!}
@else
{!!  Form::button('Update', ['type'=>'submit', 'name' => 'action', 'value'=>'update', 'id' => "visitUpdateBTN", 'class' => 'btn btn-lg btn-default']) !!}
@endif