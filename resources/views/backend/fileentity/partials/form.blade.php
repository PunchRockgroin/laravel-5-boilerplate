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
        </div><!--form control-->

    </div><!-- /.box-body -->
</div><!--box-->