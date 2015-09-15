var argv = require('yargs').argv;

var gulp = require('gulp');

// Plugins.
var jshint = require('gulp-jshint');
var stylish = require('jshint-stylish');
var sass = require('gulp-sass');

var sourcemaps = require('gulp-sourcemaps');
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
  './web/app/*.js',
  './web/app/**/*.js',
  './web/app/**/**/*.js',
  './web/app/**/**/**/*.js',
  './web/app/**/**/**/**/*.js'
];
var adminJsAssets = [
  './web/assets/libs/jquery-*.min.js',
  './web/assets/libs/angular-1.2.16.min.js',
  './web/assets/libs/*.js',
  './web/assets/modules/**/*.js'
];
var adminBuildDir = './web/assets/build';
var sassPath = './web/sass/*.scss';

/**
 * Run Javascript through JSHint.
 */
gulp.task('jshint', function() {
  return gulp.src(adminJsPath)
    .pipe(jshint())
    .pipe(jshint.reporter(stylish));
});

/**
 * Build single app.js file.
 */
gulp.task('adminAppJs', function () {
  gulp.src(adminJsPath)
    .pipe(concat('app.js'))
    .pipe(ngAnnotate())
    .pipe(uglify())
    .pipe(rename({extname: ".min.js"}))
    .pipe(header(banner, { pkg : pkg } ))
    .pipe(gulp.dest(adminBuildDir))
});

/**
 * Build single app.js file.
 */
gulp.task('adminAssetsJs', function () {
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
gulp.task('sass', function () {
  gulp.src(sassPath)
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: [
        './web/sass/compass-mixins/lib'
      ]
    }).on('error', sass.logError))
    .pipe(gulp.dest(adminBuildDir));
});

var templatesPath = './web/templates/';

var templates = {
  'screens': {
    'default': ['five-sections', 'three-columns', 'two-columns'], // ignore full-screen and full-screen-portrait, since they have no .scss files.
    'dokk1': ['wayfinding-eleven-rows', 'wayfinding-five-rows', 'wayfinding-four-rows', 'wayfinding-seven-rows', 'wayfinding-six-rows', 'wayfinding-three-rows'],
    'mso': ['mso-five-sections']
  },
  'slides': {
    'aarhus': ['rss-aarhus'],
    'default': ['ik-iframe', 'manual-calendar', 'only-image', 'only-video', 'portrait-text-top', 'rss-default', 'text-bottom', 'text-left', 'text-right', 'text-top'],
    'dokk1': ['dokk1-info', 'dokk1-multiple-calendar', 'dokk1-single-calendar', 'wayfinding'],
    'mso': ['event-calendar', 'header-top', 'mso-iframe']
  }
};

/**
 * Process SCSS using libsass
 */
gulp.task('sassTemplates', function () {
  // Iterates through the screen and slide templates defined in templates variable, and compresses each one.
  for (var templateType in templates) {
    if (templates.hasOwnProperty(templateType)) {
      for (var folder in templates[templateType]) {
        if (templates[templateType].hasOwnProperty(folder)) {
          var arr = templates[templateType][folder];

          arr.forEach(function (element) {
            gulp.src(templatesPath + '/' + templateType + '/' + folder + '/' + element + '/' + element + '.scss')
              .pipe(sass({
                outputStyle: 'compressed',
                includePaths: [
                  './web/sass/compass-mixins/lib'
                ]
              }).on('error', sass.logError))
              .pipe(gulp.dest(templatesPath + '/' + templateType + '/' + folder + '/' + element + '/'));
          });
        }
      }
    }
  }
});
