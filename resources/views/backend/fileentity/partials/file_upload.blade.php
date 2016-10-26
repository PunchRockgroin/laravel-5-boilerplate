<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">File Upload</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
            <div class="dropzone dz-clickable dz-default" id="file-upload">
                <div class="fallback hidden">
                    <input name="file" type="file" multiple />
                </div> 
                <div class="dz-message">
                    <span class="h2"><i class="fa fa-cloud-upload"></i> Drop File Here</span>
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

            </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->
