<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', app_name())</title>
        @yield('meta')

        @yield('before-styles-end')
        <style>
            <?php 
            include(public_path().'/css/backend.css');
            //include(public_path().'/bower_components/admin-lte/dist/css/skins/skin-green-light.min.css');
            include(public_path().'/css/backend_print.css');
            ?>
            </style>
        @yield('after-styles-end')
    </head>
    <body class="skin-green-light" onload="@if( config('hopper.print.location', 'internal') === 'internal')  setTimeout(function () { window.print(); }, 500);
    window.onfocus = function () { setTimeout(function () { window.close(); }, 500); } @endif">
        <div class="">
            <!-- Content Wrapper. Contains page content -->
            <div class="invoice">
                @yield('before-content')  

                @yield('content')

                @yield('after-content')
            </div><!-- /.content-wrapper -->
        </div><!-- ./wrapper -->
    </body>
</html>