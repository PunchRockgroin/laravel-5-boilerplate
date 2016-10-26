<div class="">
    {!! Form::hidden('user_name', auth()->user()->name ) !!}
    {!! Form::hidden('filename', $visit->working_filename ) !!}
    <div class="dropzone dz-clickable dz-default" id="visit-upload">
        <div class="fallback hidden">
            <input name="file" type="file" multiple />
        </div> 
        <div class="dz-message">
            <span class="h2"><i class="fa fa-upload"></i> Drop updated file here</span>
        </div>
    </div>
    <div id="preview-template" style="display: none;"> 
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-cog fa-spin"></i></span>
            <div class="info-box-content">
                <div class="info-box-header ">
                    <div class="alert alert-info">
                        <strong><span data-dz-name></span></strong> <span class="renamed-to"></span>
                    </div>
                </div>
                <div class="info-box-number">

                    <div class="" style="height:20px;"><span class="progress-bar progress-bar-primary progress-bar-striped" data-dz-uploadprogress></span></div>
                    <div class="dz-size" data-dz-size></div>
                    <br />
                </div>
                @if(config('hopper.dropbox_enable', false))
                <div class="info-box-text dz-wait">

                </div>
                @endif
                <div class="info-box-text dz-error">
                    <span data-dz-errormessage></span>
                </div>
                <div class="clearfix">
                    <div class="pull-right">
                        <a class="btn btn-warning" data-dz-remove><i class="fa fa-remove"></i> Remove</a> 
                    </div>
                </div>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
	<div id='usingHopperClient' class='hidden'>
			<div class='alert alert-info'>
				<i class="fa fa-info-circle" ></i> Using Hopper Client
			</div>
		{!! Form::hidden('using_hopper_client', 'false', ['class' => 'form-control']) !!}	
	</div>
    </div>
</div>