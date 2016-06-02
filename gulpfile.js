var argv = require('yargs').argv;

var gulp = require('gulp-help')(require('gulp'));

// Plugins.
var jshint = require('gulp-jshint');
var stylish = require('jshint-stylish');
var sass = require('gulp-sass');

var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var ngAnnotate = require('gulp-ng-annotate');
var rename = require('gulp-rename');
var gulpif = require('gulp-if');

var header = require('gulp-header');
var pkg = require('./version.json');
var banner = ['/**',
  ' * @name <%= pkg.name %>',
  ' * @version v<%= pkg.version %>',
  ' * @link <%= pkg.link %>',
  ' */',
  ''].join('\n');

// We only want to process our own non-processed JavaScript files.
var adminJsPath = [
  './web/app/**/**/**/**/*.js'
];

var adminJsAssets = [
  './web/assets/libs/jquery-*.min.js',
  './web/assets/libs/angular-1.*.min.js',
  './web/assets/libs/angular-animate-1.*.min.js',
  './web/assets/libs/angular-route-1.*.min.js',
  './web/assets/libs/angular-route-1.*.min.js',
  './web/assets/libs/angular-bootstrap-colorpicker*.js',
  './web/assets/libs/angular-css-injector*.js',
  './web/assets/libs/angular-file-upload*.js',
  './web/assets/libs/datetimepicker*.js',
  './web/assets/libs/es5-shim*.js',
  './web/assets/libs/locale_da*.js',
  './web/assets/libs/moment.min.js',
  './web/assets/libs/moment_da_locale.js',
  './web/assets/libs/stacktrace*.js'
];

var adminBuildDir = './web/assets/build';
var sassPath = './web/sass/*.scss';
var sassWatchPath = './web/sass/**/*.scss';

/**
 * Run Javascript through JSHint.
 */
gulp.task('jshint', 'Runs JSHint on js', function () {
  return gulp.src(adminJsPath)
    .pipe(jshint())
    .pipe(jshint.reporter(stylish));
});

/**
 * Build single app.js file.
 */
gulp.task('js', 'Build all custom js files into one js file. If --production is set minify the js as well.', function () {
    gulp.src(adminJsPath)
      .pipe(concat('app.js'))
      .pipe(ngAnnotate())
      .pipe(gulpif(argv.production, uglify()))
      .pipe(rename({extname: ".min.js"}))
      .pipe(header(banner, {pkg: pkg}))
      .pipe(gulp.dest(adminBuildDir))
  }
);

/**
 * Build single assets.js file.
 */
gulp.task('assets', 'Build all asset js files into one js file. If --production is set minify the js as well.', function () {
  gulp.src(adminJsAssets)
    .pipe(concat('assets.js'))
    .pipe(ngAnnotate())
    .pipe(gulpif(argv.production, uglify()))
    .pipe(rename({extname: ".min.js"}))
    .pipe(gulp.dest(adminBuildDir))
});

/**
 * Process SCSS using libsass
 */
gulp.task('sass', 'Compile sass into css. If --production is set, minify the css as well.', function () {
  // If not run in production mode, compile the sass into a non-minified css file.
  gulp.src(sassPath)
    .pipe(gulpif(!argv.production, sass({
      includePaths: [
        './web/sass/compass-mixins/lib'
      ]
    }).on('error', sass.logError)))
    .pipe(gulpif(!argv.production, gulp.dest(adminBuildDir)));

  // If run in production mode, compile and minify the css.
  gulp.src(sassPath)
    .pipe(gulpif(argv.production, sass({
      outputStyle: 'compressed',
      includePaths: [
        './web/sass/compass-mixins/lib'
      ]
    }).on('error', sass.logError)))
    .pipe(gulpif(argv.production, rename({extname: ".min.css"})))
    .pipe(gulpif(argv.production, gulp.dest(adminBuildDir)));
});

/**
 * Watch files for changes and run tasks.
 */
gulp.task('watch', 'Starts a watch to compile sass and js. For use in development.', function () {
  gulp.watch(adminJsPath, ['jshint']);
  gulp.watch(sassWatchPath, ['sass']);
});


var templatesPath = './web/templates/';

// @TODO: Discover this structure automatically
var templates = {
  'screens': {
    'default': ['five-sections', 'three-columns', 'two-columns'],
    'dokk1': ['wayfinding-eleven-rows', 'wayfinding-five-rows', 'wayfinding-four-rows', 'wayfinding-seven-rows', 'wayfinding-six-rows', 'wayfinding-three-rows', 'itk-three-split'],
    'mso': ['mso-five-sections', 'mso-four-sections'],
    'mbu': ['mbu-three-split'],
    'itk': ['itk-three-split']
  },
  'slides': {
    'aarhus': ['rss-aarhus'],
    'default': ['ik-iframe', 'manual-calendar', 'only-image', 'only-video', 'portrait-text-top', 'rss-default', 'text-bottom', 'text-left', 'text-right', 'text-top'],
    'dokk1': ['dokk1-info', 'dokk1-multiple-calendar', 'dokk1-single-calendar', 'wayfinding', 'dokk1-instagram', 'dokk1-coming-events'],
    'mso': ['event-calendar', 'header-top', 'mso-iframe'],
    'mbu': ['mbu-header', 'mbu-footer']
  }
};

/**
 * Process SCSS using libsass
 */
gulp.task('sassTemplates', 'Compile the sass for each templates into minified css files.', function () {
  // Iterates through the screen and slide templates defined in templates variable, and compresses each one.
  for (var templateType in templates) {
    if (templates.hasOwnProperty(templateType)) {
      for (var folder in templates[templateType]) {
        if (templates[templateType].hasOwnProperty(folder)) {
          var arr = templates[templateType][folder];

          arr.forEach(function (element) {
            gulp.src(templatesPath + '/' + folder + '/' + templateType + '/' + element + '/' + element + '.scss')
              .pipe(sass({
                outputStyle: 'compressed',
                includePaths: [
                  './web/sass/compass-mixins/lib'
                ]
              }).on('error', sass.logError))
              .pipe(gulp.dest(templatesPath + '/' + folder + '/' + templateType + '/' + element + '/'));
          });
        }
      }
    }
  }
});
