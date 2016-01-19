@extends('backend.layouts.master')

@section('title', app_name() .' | '. trans('fileentity.backend.sidebar.title') .' | '. trans('fileentity.backend.sidebar.index'))

@section('page-header')
    <h1>
        {!! app_name() !!}
        <small>{{ trans('fileentity.backend.admin.title') }} &raquo; {{ trans('fileentity.backend.admin.index') }}</small>
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table responsive table-bordered table-striped" width="100%" id="fileentities-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Filename</th>
                        <th>Storage Disk</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Operations</th>
                    </tr>
                </thead>
            </table>
        </div><!-- /.box-body -->
    </div><!--box box-success-->
@endsection



@push('after-scripts-end')
<script>
$(function() {
    $('#fileentities-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.fileentity.data') !!}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'filename', name: 'filename' },
            { data: 'storage_disk', name: 'storage_disk' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'operations', name: 'operations' ,orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush