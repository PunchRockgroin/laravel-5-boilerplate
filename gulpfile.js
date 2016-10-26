const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');
require('./elixir-extensions');


elixir(mix => {
    mix.copy(
       'node_modules/font-awesome/fonts',
       'public/build/fonts/font-awesome'
     )
     .copy(
       'node_modules/bootstrap-sass/assets/fonts/bootstrap',
       'public/build/fonts/bootstrap'
     )
     .copy(
        [
            'node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
            'node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js'
        ],
        'resources/assets/vendor/bootstrap-switch'
     )
     .copy(
        [
            'node_modules/dropzone/dist'
        ],
        'resources/assets/vendor/dropzone'
     )
//     .copy(
//       'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
//       'public/js/vendor/bootstrap'
//     )
 
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
        'frontend/app.css'
     ], 'public/css/frontend.css')

     .webpack('frontend/app.js', './resources/assets/js/dist/frontend.js')
     /**
      * Combine frontend scripts
      */
     .scripts([
        'dist/frontend.js',
        'plugin/sweetalert/sweetalert.min.js',
        'plugins.js'
       
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
         '../vendor/dropzone/dropzone.css',
         '../vendor/dropzone/basic.css',
         '../vendor/bootstrap-switch/bootstrap-switch.min.css',
//         '../bower_components/animate.css/animate.css',
         'backend/app.css'
     ], 'public/css/backend.css')
     

     /**
      * Make RTL (Right To Left) CSS stylesheet for the backend
      */
//     .rtlCss()

     .webpack('backend/app.js', './resources/assets/js/dist/backend.js')
     /**
      * Combine backend scripts
      */
     .scripts([
       'dist/backend.js',
       '../bower_components/bootstrap-daterangepicker/daterangepicker.js',
//       '../bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js',
       '../bower_components/datatables.net/js/jquery.dataTables.min.js',
//       '../bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js',
       '../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
//       '../bower_components/AdminLTE/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js',
       '../bower_components/datatables.net-responsive/js/dataTables.responsive.min.js',
       '../bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.js',
       '../bower_components/AdminLTE/plugins/chartjs/Chart.min.js',
//       '../bower_components/matchHeight/dist/jquery.matchHeight.js',
       'plugin/sweetalert/sweetalert.min.js',
       'plugins.js',
       '../vendor/bootstrap-switch/bootstrap-switch.min.js',
       '../bower_components/jquery.repeater/jquery.repeater.js',
       '../bower_components/pusher/dist/pusher.js',
       'backend/plugin/toastr/toastr.min.js',
       'backend/custom.js'
     ], 'public/js/backend.js')

    /**
      * Apply version control
      */
     .version([
         "public/css/frontend.css",
         "public/js/frontend.js",
         "public/css/backend.css",
         "public/js/backend.js"
     ]);
});
