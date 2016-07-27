<div class="form-group">
    {!! Form::label('design_username', 'Graphic Operator') !!}
    {!! Form::text('design_username', isset($visit['design_username']) ? $visit['design_username'] : auth()->user()->name, $attributes = array('class'=>'form-control', 'readonly'=>'readonly')) !!}
    <p class="help-block">Your username is automatically populated here.</p>
</div>

<div class="form-group @if ($errors->has('visitors')) has-error @endif">
    {!! Form::label('visitors', 'Name of Visitor(s)') !!}
	<p class="help-block">If empty, please get the name of the visitors during this session.</p>
	<div class="form-group">
		<div class="radio">
		<label class="control-label ">{!! Form::radio('visitor_type', 'none', null, ['id'=> 'visitorsNone']) !!}No one was present</label>
		</div>
	</div>
	<div class="form-group">
		<div class="radio">
		<label class="control-label ">{!! Form::radio('visitor_type', 'idk', null, ['id'=> 'visitorsIdk']) !!}I Do Not Know/Could Not Get Visitor Names</label>
		</div>
	</div>
	<div class="input-group">
      <span class="input-group-addon">
        {!! Form::radio('visitor_type', 'normal', null, ['id'=> 'visitorsNames']) !!}
      </span>
      {!! Form::text('visitors', null, $attributes = array('id'=>'visitorNamesEntry', 'class'=>'form-control', 'placeholder'=>'Enter visitor name(s)')) !!}
    </div><!-- /input-group -->
    <p class="help-block">If empty, please get the name of the visitors during this session.</p>
</div>
<div class="form-group @if ($errors->has('difficulty')) has-error @endif">
    {!! Form::label('difficulty', 'Status') !!}
    <br />
    <label class='radio-inline'>
        {!! Form::radio('difficulty', '1', null, ['id'=> 'difficulty-1']) !!}Click-Through
    </label>
    <label class='radio-inline'>
        {!! Form::radio('difficulty', '2', null, ['id'=> 'difficulty-2']) !!}Minor Edits
    </label>
    <label class='radio-inline'>
        {!! Form::radio('difficulty', '3', null, ['id'=> 'difficulty-3']) !!}New Slides
    </label>
    <p class="help-block">Please choose the level of changes made during this visit</p>
</div>
<div class="form-group">
    {!! Form::label('design_notes', 'Design Notes') !!}
    {!! Form::textarea('design_notes', null, $attributes = array('class'=>'form-control')) !!}
    <p class="help-block">Please be descriptive but brief</p>
</div>  