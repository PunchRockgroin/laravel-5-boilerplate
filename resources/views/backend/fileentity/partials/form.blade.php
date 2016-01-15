<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">File Info</h3>
    </div><!-- /.box-header -->

    <div class="box-body">

        <div class="form-group">
            {!! Form::label('filename', trans('fileentity.backend.form.filename.label'), ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
                {!! Form::text('filename', null,['class' => 'form-control', 'placeholder' => trans('fileentity.backend.form.filename.placeholder')]) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('mime', 'Mime', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
                {!! Form::text('mime', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('session_id', 'Session ID', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
                {!! Form::text('session_id', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('storage_disk', 'Storage Disk', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
                {!! Form::text('storage_disk', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('path', 'Path', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
                {!! Form::text('path', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('status', 'Status', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
                {!! Form::text('status', null,['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('metadata', 'MetaData', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
                {!! Form::text('metadata', null,['class' => 'form-control']) !!}
            </div>
        </div><!--form control-->
        <div class='clearfix'>
            {!!  Form::submit('Submit', ['class' => 'btn btn-success pull-right']) !!}
        </div>
    </div><!-- /.box-body -->
</div><!--box-->