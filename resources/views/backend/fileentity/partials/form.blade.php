<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">File Info</h3>
    </div><!-- /.box-header -->
    @if(!empty($FileEntity))
        {!! Form::hidden('currentfilename', $FileEntity->filename) !!}
    @endif
    <div class="box-body">

        <div class="form-group">
            {!! Form::label('filename', trans('fileentity.backend.form.filename.label'), ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('filename', null,['class' => 'form-control', 'placeholder' => trans('fileentity.backend.form.filename.placeholder')]) !!}
                <div class="help-block">{{ trans('fileentity.backend.form.filename.help_block') }}</div>
            </div>
        </div>
        @if(!empty($FileEntity))
        <div class="form-group">
            {!! Form::label('next_version', trans('fileentity.backend.form.next_version.label'), ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::versionRange('next_version', null, $nextVersion, ['class' => 'form-control']) !!}
                <div class="help-block">{{ trans('fileentity.backend.form.next_version.help_block') }}</div>
            </div>
        </div>
        @endif
        <div class="form-group">
            {!! Form::label('mime', 'Mime', ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('mime', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('session_id', 'Session ID', ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('session_id', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('storage_disk', 'Storage Disk', ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('storage_disk', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('path', 'Path', ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('path', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('status', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('data', 'Data', ['class' => 'control-label']) !!}
            <div class="">
                {!! Form::text('data', null,['class' => 'form-control']) !!}
            </div>
        </div><!--form control-->
        <div class='clearfix'>
            {!!  Form::submit('Submit', ['name' => 'action' ,'class' => 'btn btn-success pull-right']) !!}
        </div>
    </div><!-- /.box-body -->
</div><!--box-->