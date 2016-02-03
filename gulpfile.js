var elixir = require('laravel-elixir');

elixir(function(mix) {
 mix
//     .phpUnit()

    /**
     * Copy needed files from /node directories
     * to /public directory.
     */
     .copy(
       'node_modules/font-awesome/fonts',
       'public/build/fonts/font-awesome'
     )
     .copy(
       'node_modules/bootstrap-sass/assets/fonts/bootstrap',
       'public/build/fonts/bootstrap'
     )
     .copy(
       'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
       'public/js/vendor/bootstrap'
     )
     
     .copy('resources/assets/brand/hpe/font', 'public/build/fonts/hpe')

     /**
      * Process frontend SCSS stylesheets
      */
     .sass([
        'frontend/app.scss',
        'plugin/sweetalert/sweetalert.scss'
     ], 'resources/assets/css/frontend/app.css')

     /**
      * Combine pre-processed frontend CSS files
      */
     .styles([
        'frontend/app.css'
     ], 'public/css/frontend.css')

     /**
      * Combine frontend scripts
      */
     .scripts([
        'plugin/sweetalert/sweetalert.min.js',
        'plugins.js',
        'frontend/app.js'
     ], 'public/js/frontend.js')

     /**
      * Process backend SCSS stylesheets
      */
     .sass([
         'backend/app.scss',
         'backend/plugin/toastr/toastr.scss',
         'plugin/sweetalert/sweetalert.scss'
     ], 'resources/assets/css/backend/app.css')

     /**
      * Combine pre-processed backend CSS files
      */
     .styles([
         '../bower_components/bootstrap-daterangepicker/daterangepicker.css',
         '../bower_components/AdminLTE/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css',
         '../bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css',         
         '../bower_components/dropzone/dist/min/dropzone.min.css',
         '../bower_components/dropzone/dist/min/dropzone.min.css',
         '../bower_components/dropzone/dist/min/basic.min.css',
         '../bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
         'backend/app.css'
     ], 'public/css/backend.css')

     /**
      * Combine backend scripts
      */
     

     .scripts([
         
         'assets/bower_components/AdminLTE/plugins/jQueryUI/jquery-ui.js',
         'assets/bower_components/moment/moment.js',
         'assets/bower_components/bootstrap-daterangepicker/daterangepicker.js',
         'assets/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js',
         'assets/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js',
         'assets/bower_components/AdminLTE/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js',         
         'assets/js/plugin/sweetalert/sweetalert.min.js',
         'assets/js/plugins.js',         
         'assets/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js',
         'assets/bower_components/dropzone/dist/min/dropzone.min.js',
         'assets/bower_components/jquery.repeater/jquery.repeater.js',
//         'assets/bower_components/repeatable-fields/repeatable-fields.js',
         'assets/js/backend/app.js',
         'assets/js/backend/plugin/toastr/toastr.min.js',
         'assets/js/backend/custom.js'
     ], 'public/js/backend.js', 'resources')

    /**
      * Apply version control
      */
     .version(["public/css/frontend.css", "public/js/frontend.js", "public/css/backend.css", "public/js/backend.js"]);
});