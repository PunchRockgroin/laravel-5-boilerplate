@include('backend.fileentity.partials.file_upload')
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Event Session File Data</h3>
	</div>
	<div class="box-body">
		@if(count($eventsession['session_files']) > 1)
		<div class='multiple-file-section'>
		<h4>Files in Master</h4>
		<div class="alert alert-sm alert-warning">The files below are located in master, and match the session. Please choose one to use.</div>
		@foreach ($eventsession['session_files'] as $key => $session_file)
			{!! Bootstrap::radio('session_file', $session_file['filename'], $session_file['filename'], NULL, ['id'=>'sessionFile_'.$key,'class'=>'session_file_option', 'data-nextVersion' => $session_file['nextVersion']]) !!}
		@endforeach
		</div>
		@elseif(!count($eventsession['session_files']))
		<div class="info-box">
			<span class="info-box-icon bg-red"><i class="fa fa-check-circle"></i></span>

			<div class="info-box-content">
				<span class="info-box-text">File Status</span>
				<span class="info-box-number">There is no file associated with this Session. Please see Tech Support.</span>
			</div>
			<!-- /.info-box-content -->
		</div>
		@else
		
		@endif
		<div class='current-file-section @if(count($eventsession['session_files']) > 1) hidden @endif'>
			{!! Form::label('currentfilename', 'Current '.trans('fileentity.backend.form.filename.label'), ['class' => 'control-label']) !!}
			{!! Form::text('currentfilename', isset($eventsession['session_files']->first()['filename']) ? $eventsession['session_files']->first()['filename'] : null, ['class' => 'form-control','readonly'=>'readonly', 'placeholder' => trans('fileentity.backend.form.filename.placeholder')]) !!}
			<div class="help-block">The Current File in Master</div>
			<div class='current-file-pdf-section @if(!config('hopper.generate_pdf_mode')) hidden @endif'>
				@if($session_pdf) 
				<p>This event session has a generated PDF: {{ $session_pdf }}</p>
				@else
				<div class="alert alert-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> This event session does not have a generated PDF</div>
				@endif
			</div>
		</div>
		<div class='file-update-section hidden'>
			<div class="form-group hidden">
				{!! Form::label('next_version', trans('fileentity.backend.form.next_version.label'), ['class' => 'control-label']) !!}
				<div class="">
					{!! Form::versionRange('next_version', null, isset($eventsession['session_files']->first()['nextVersion']) ? $eventsession['session_files']->first()['nextVersion'] : '0', ['class' => 'form-control']) !!}
					<div class="help-block">{{ trans('fileentity.backend.form.next_version.help_block') }}</div>
				</div>
			</div>
			<div class="form-group">
            {!! Form::label('filename', 'New '.trans('fileentity.backend.form.filename.label'), ['class' => 'control-label']) !!}
            <div class="">
									
				{!! Form::hidden('temporaryfilename', null, ['class' => 'form-control']) !!}		
				{!! Form::hidden('filename_uploaded', null, ['class' => 'form-control']) !!}		
				{!! Form::hidden('_id', $eventsession['id'], ['class' => 'form-control', 'readonly'=>'readonly']) !!}		
                {!! Form::text('filename', isset($eventsession['session_files']->first()['filename']) ? $eventsession['session_files']->first()['filename'] : null, ['class' => 'form-control', 'readonly'=>'readonly', 'placeholder' => trans('fileentity.backend.form.filename.placeholder')]) !!}
				<div class="help-block">This will be the new file name</div>
            </div>
			</div>
		</div>
	</div>
</div>	