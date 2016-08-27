<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="form-group">
    {!! Form::label($name.'-pseudo', $label, $labelAttributes) !!} <br />
    {!! Form::checkbox($name.'-pseudo', true, $checked, $switchAttributes) !!}
    {!! Form::hidden($name, $value) !!}
	@if($description)
	<div class="description">{!! $description !!}</div>
	@endif
</div>