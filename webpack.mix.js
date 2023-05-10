let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.styles([
	"resources/frontend/css/flatpickr.min.css",
	"resources/frontend/css/select2.min.css",
	"resources/frontend/css/bootstrap.min.css",
	"resources/frontend/css/style.css",
	"resources/frontend/css/font-awesome.css",
	"resources/frontend/css/et-line-fonts.css",
	"resources/frontend/css/owl.carousel.css",
	"resources/frontend/css/owl.style.css",
	"resources/frontend/css/flaticon.css",
	// "resources/frontend/css/magnific-popup.css",
	"resources/frontend/css/defualt.css",
	"resources/frontend/css/animate.min.css",
	"resources/frontend/css/bootstrap-dropdownhover.min.css",
	"resources/frontend/css/intlTelInput.css",
	"resources/backend/css/sweetalert2.min.css",
	], 'public/frontend_assets/css/bundled.css').options({
   postCss: [
      require('postcss-discard-comments')({
         removeAll: true
      })
   ]
}
);

mix.babel([
    "resources/frontend/js/jquery.min.js",
	"resources/frontend/js/popper.min.js",
	"resources/frontend/js/bootstrap.min.js",
	"resources/frontend/js/bootstrap-dropdownhover.min.js",
	"resources/frontend/js/easing.js",
	"resources/frontend/js/jquery.countTo.js",
	"resources/frontend/js/jquery.waypoints.js",
	"resources/frontend/js/jquery.appear.min.js",
	// "resources/frontend/js/jquery.shuffle.min.js",
	// "resources/frontend/js/carousel.min.js",
	"resources/frontend/js/jquery-migrate.min.js",
	// "resources/frontend/js/color-switcher.js",
	// "resources/frontend/js/jquery.magnific-popup.min.js",
	// "resources/frontend/js/theia-sticky-sidebar.js",
	"resources/frontend/js/app.js",
	"resources/frontend/js/jquery.counterup.js",
	"resources/frontend/js/parsley.min.js",
	"resources/frontend/js/sweetalert2.min.js",
	"resources/frontend/js/jquery.form.js",
	"resources/frontend/js/select2.min.js",
	"resources/frontend/js/flatpickr.js"
], 'public/frontend_assets/js/bundled.js');



// //admin setup
// mix.styles([
//    'resources/backend/css/flatpickr.min.css',
//    'resources/backend/css/sweetalert2.min.css',
//    'resources/backend/css/bootstrap.min.css',
//    'resources/backend/css/icons.min.css',
//    'resources/backend/css/app.min.css',
//    'resources/backend/css/dataTables.bootstrap4.css',
//    'resources/backend/css/buttons.bootstrap4.css',
//    'resources/backend/css/select2.min.css'
// ], 'public/admin_assets/css/bundled.min.css').options({
//    postCss: [
//       require('postcss-discard-comments')({
//          removeAll: true
//       })
//    ]
// });

// mix.babel([
//    	"resources/backend/js/vendor.min.js",
// 	"resources/backend/js/parsley.min.js",
// 	"resources/backend/js/select2.min.js",
// 	"resources/backend/js/sweetalert2.min.js",
// 	"resources/backend/js/jquery.form.js"
// ], 'public/admin_assets/js/bundled.min.js');


// mix.babel([
// 	"resources/backend/datatable/jquery.dataTables.min.js",
// 	"resources/backend/datatable/dataTables.bootstrap4.js",
// 	"resources/backend/datatable/dataTables.buttons.min.js",
// 	"resources/backend/datatable/buttons.bootstrap4.min.js",
// 	"resources/backend/datatable/buttons.html5.min.js",
// 	"resources/backend/datatable/buttons.flash.min.js",
// 	"resources/backend/datatable/buttons.print.min.js",
// 	// "resources/backend/datatable/pdfmake.min.js",
// 	// "resources/backend/datatable/vfs_fonts.js"
// ], 'public/admin_assets/js/dataTable_bundled.min.js');

