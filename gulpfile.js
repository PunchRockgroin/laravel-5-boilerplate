var elixir = require('laravel-elixir');
require('./elixir-extensions');

elixir(function(mix) {
 mix
     //.phpUnit()
     //.compressHtml()

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
 
     /**
     * Copy needed files from Datatables Bower directories
     * to /public directory.
     */
     .copy(
        [
            'resources/assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js',
            'resources/assets/bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.js'
        ],
       'public/js/backend/plugin/datatables'
     )
     
     .copy(
        [
            'resources/assets/bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css',
        ],
       'public/css/backend/plugin/datatables'
     )
     /**
     * Copy needed files from Branding
     * to /public directory.
     */
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
       '../bower_components/bootstrap-daterangepicker/daterangepicker.css',
       '../bower_components/AdminLTE/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css',
       '../bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css',
       '../bower_components/dropzone/dist/min/dropzone.min.css',
       '../bower_components/dropzone/dist/min/dropzone.min.css',
       '../bower_components/dropzone/dist/min/basic.min.css',
       '../bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
       '../bower_components/animate.css/animate.css',
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

     .sass([
         'brand/font_inline.scss'
     ], 'public/css/backend_print.css')

     /**
      * Combine pre-processed backend CSS files
      */
     .styles([
         '../bower_components/bootstrap-daterangepicker/daterangepicker.css',
         '../bower_components/datatables.net-bs/css/dataTables.bootstrap.css',
         '../bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.css',
         '../bower_components/dropzone/dist/min/dropzone.min.css',
         '../bower_components/dropzone/dist/min/basic.min.css',
         '../bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
         '../bower_components/animate.css/animate.css',
         'backend/app.css'
     ], 'public/css/backend.css')

     /**
      * Make RTL (Right To Left) CSS stylesheet for the backend
      */
     .rtlCss()

     /**
      * Combine backend scripts
      */
     .scripts([
       '../bower_components/lodash/lodash.js',
       '../bower_components/moment/moment.js',
       '../bower_components/bootstrap-daterangepicker/daterangepicker.js',
//       '../bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js',
       '../bower_components/datatables.net/js/jquery.dataTables.min.js',
//       '../bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js',
       '../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
//       '../bower_components/AdminLTE/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js',
       '../bower_components/datatables.net-responsive/js/dataTables.responsive.min.js',
       '../bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.js',
       '../bower_components/AdminLTE/plugins/chartjs/Chart.min.js',
       '../bower_components/matchHeight/dist/jquery.matchHeight.js',
       '../bower_components/vue/dist/vue.js',
       '../bower_components/vue-resource/dist/vue-resource.js',
       'plugin/sweetalert/sweetalert.min.js',
       'plugins.js',
       '../bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js',
       '../bower_components/dropzone/dist/min/dropzone.min.js',
       '../bower_components/jquery.repeater/jquery.repeater.js',
       '../bower_components/pusher/dist/pusher.js',
       'backend/app.js',
       'backend/plugin/toastr/toastr.min.js',
       'backend/custom.js'
     ], 'public/js/backend.js')

     /**
      * Combine pre-processed rtl CSS files
      */
     .styles([
         'rtl/bootstrap-rtl.css'
     ], 'public/css/rtl.css')

    /**
      * Apply version control
      */
     .version([
         "public/css/frontend.css",
         "public/js/frontend.js",
         "public/css/backend.css",
         "public/css/backend-rtl.css",
         "public/js/backend.js",
         "public/css/rtl.css"
     ]);
});
