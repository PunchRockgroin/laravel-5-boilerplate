@extends('backend.layouts.app')

@section ('title',  app_name() . ' | Import Event Sessions' )

@section('page-header')
<h1>
	{{ app_name() }}
	<small>Import Event Sessions</small>
</h1>
@endsection

@section('breadcrumbs')
<li><a href="{!!route('admin.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
<li class="active">Here</li>
@endsection

@section('content')
{!! Form::open(array('route' => 'backend.hopper.admin.import.process', 'role'=>'form', 'id'=>'hopperAdminForm')) !!}

{!! Bootstrap::info('The columns "' . implode(', ', config('hopper.import.required_headers') ).'" are required', 'Heads up!'); !!}

@include('backend.hopper.admin.partials.file_upload')
{!! Form::hidden('model', 'eventsessions') !!}
{!! Form::hidden('filename') !!}
<div class='file-update-section hidden'>
	{!! Bootstrap::submit('Begin Import', ['class'=>'btn btn-lg btn-primary btn-block']) !!}
</div>
{!! Form::close() !!}
@endsection


@section('after-scripts-end')
<script>
    $( function () {
//		Dropzone.autoDiscover = true;
        var importUploadDz = new Dropzone( "div#import-upload", {
            url: "{{ route('backend.hopper.admin.import.upload', 'eventsessions') }}",
            previewTemplate: document.querySelector( '#preview-template' ).innerHTML,
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 500, // MB
            maxFiles: 1,
            acceptedFiles: '.xls,.xlsx',
            init: function () {
				this.on("reset", function(file){
					$('.file-update-section').addClass('hidden');
				});
                this.on( "success", function ( file, response ) {
//                    $('.file-update-section').removeClass('hidden');
                    $('input[name="filename"]').val(response.payload.upload.path);
                    var $el = $(file.previewElement);
                    $el.find('.info-box-icon')
                            .toggleClass('bg-aqua bg-green')
                            .find('i')
                            .toggleClass('fa-cog fa-spin fa-check-circle-o');
                    $el.find('.message')
                            .html('has verified required column headers. Imported ' + response.payload.count + ' sessions.')
                            ;
                } );
                this.on( "error", function ( file, response ) {
                    var $el = $( file.previewElement );
                    $el.find( '.info-box-icon' )
                        .toggleClass( 'bg-aqua bg-red' )
                        .find( 'i' )
                        .toggleClass( 'fa-cog fa-spin fa-times-circle-o' );
                    $el.find( '.dz-error' )
                        .wrapInner( '<div class="alert alert-danger" />' );
                } );
            },
            params: {
                _token: "{{ csrf_token() }}",
            }
        } );
    } );

</script>
@endsection
