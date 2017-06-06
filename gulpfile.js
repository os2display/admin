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
  './web/app/app.js',
  './web/app/**/**/**/**/*.js'
];

var adminJsAssets = [
  './web/assets/libs/jquery.min.js',
  './web/assets/libs/angular.min.js',
  './web/assets/libs/angular-animate.min.js',
  './web/assets/libs/angular-bootstrap-colorpicker.js',
  './web/assets/libs/angular-file-upload.min.js',
  './web/assets/libs/angular-placeholder.js',
  './web/assets/libs/angular-route.min.js',
  './web/assets/libs/datetimepicker.jquery.js',
  './web/assets/libs/datetimepicker.js',
  './web/assets/libs/es5-shim.min.js',
  './web/assets/libs/locale_da.js',
  './web/assets/libs/md5.min.js',
  './web/assets/libs/moment.min.js',
  './web/assets/libs/moment_da_locale.js',
  './web/assets/libs/paging.js',
  './web/assets/libs/stacktrace.min.js'
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
gulp.task('js', 'Build all custom js files into one minified js file.', function () {
    gulp.src(adminJsPath)
      .pipe(concat('app.js'))
      .pipe(ngAnnotate())
      .pipe(uglify())
      .pipe(rename({extname: ".min.js"}))
      .pipe(header(banner, {pkg: pkg}))
      .pipe(gulp.dest(adminBuildDir))
  }
);

/**
 * Build single assets.js file.
 */
gulp.task('assets', 'Build all asset js files into one minified js file.', function () {
  gulp.src(adminJsAssets)
    .pipe(concat('assets.js'))
    .pipe(ngAnnotate())
    .pipe(uglify())
    .pipe(rename({extname: ".min.js"}))
    .pipe(gulp.dest(adminBuildDir))
});

/**
 * Process SCSS using libsass
 */
gulp.task('sass', 'Compile sass into minified css', function () {
  gulp.src(sassPath)
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: [
        './web/sass/compass-mixins/lib'
      ]
    }).on('error', sass.logError))
    .pipe(rename({extname: ".min.css"}))
    .pipe(gulp.dest(adminBuildDir));
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
  'default_templates': {
    'screens': ['five-sections', 'three-columns', 'two-columns', 'full-screen', 'two-columns', 'full-screen-portrait', 'four-sections'],
    'slides': ['ik-iframe', 'manual-calendar', 'only-image', 'only-video', 'portrait-text-top', 'rss-default', 'text-bottom', 'text-left', 'text-right', 'text-top']
  },
  'ding2': {
    'slides': ['ding-events', 'opening-hours']
  }
};

/**
 * Process SCSS using libsass
 */
gulp.task('sassTemplates', 'Compile the sass for each templates into minified css files.', function () {
  // Iterates through the screen and slide templates defined in templates variable, and compresses each one.
  for (var folder in templates) {
    for (var type in templates[folder]) {
      templates[folder][type].forEach(function (element) {
        var path = templatesPath + folder + '/' + type + '/' + element + '/';// + folder + '/' + templateType + '/' + element + '/';
        gulp.src(path + element + '.scss')
          .pipe(sass({
            outputStyle: 'compressed',
            includePaths: [
              './web/sass/compass-mixins/lib'
            ]
          }).on('error', sass.logError))
          .pipe(gulp.dest(path));
      });
    }
  }
});
