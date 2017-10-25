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
var yaml = require('js-yaml');
var fs = require('fs');

var header = require('gulp-header');
var pkg = require('./version.json');
var banner = ['/**',
  ' * @name <%= pkg.name %>',
  ' * @version v<%= pkg.version %>',
  ' * @link <%= pkg.link %>',
  ' */',
  ''].join('\n');

// We only want to process our own non-processed JavaScript files.
var adminJsPath = (function() {
  var configFiles = [
    'app/config/modules.yml',
    'app/config/apps.yml'
  ],
  jsFiles = [
    'app/app.js'
  ],

  // Breadth-first descend into data to find "files".
  buildFiles = function(data) {
    if (typeof(data) === 'object') {
      for (var p in data) {
        if (p === 'files') {
          jsFiles = jsFiles.concat(data[p]);
        } else {
          buildFiles(data[p]);
        }
      }
    }
  };

  configFiles.forEach(function(path) {
    var data = yaml.safeLoad(fs.readFileSync(path, 'utf8'));
    buildFiles(data);
  });

  return jsFiles.map(function (file) {
    return 'web/' + file;
  });
}());

var adminJsAssets = [
  './web/assets/libs/jquery.min.js',
  './web/assets/libs/angular.min.js',
  './web/assets/libs/angular-animate.min.js',
  './web/assets/libs/angular-bootstrap-colorpicker.js',
  './web/assets/libs/angular-file-upload.min.js',
  './web/assets/libs/angular-placeholder.js',
  './web/assets/libs/angular-route.min.js',
  './web/assets/libs/angular-dnd.js',
  './web/assets/libs/angular-tooltips.min.js',
  './web/assets/libs/angular-modal-service.min.js',
  './web/assets/libs/angular-translate.min.js',
  './web/assets/libs/angular-translate-loader-static-files.min.js',
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
  return gulp.src(adminJsPath)
      .pipe(concat('app.js'))
      .pipe(ngAnnotate())
      .pipe(uglify())
      .pipe(rename({extname: ".min.js"}))
      .pipe(header(banner, {pkg: pkg}))
      .pipe(gulp.dest(adminBuildDir));
  }
);

/**
 * Build single app.js file.
 */
gulp.task('js-src', 'Report all source files for "js" task.', function () {
  adminJsPath.forEach(function (path) {
    process.stdout.write(path + '\n');
  });
});

/**
 * Build single assets.js file.
 */
gulp.task('assets', 'Build all asset js files into one minified js file.', function () {
  return gulp.src(adminJsAssets)
    .pipe(concat('assets.js'))
    .pipe(ngAnnotate())
    .pipe(uglify())
    .pipe(rename({extname: ".min.js"}))
    .pipe(gulp.dest(adminBuildDir));
});

/**
 * Process SCSS using libsass
 */
gulp.task('sass', 'Compile sass into minified css', function () {
  return gulp.src(sassPath)
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
  'screens': {
    'default_templates': ['five-sections', 'three-columns', 'two-columns'],
    'dokk1_templates': ['wayfinding-eleven-rows', 'wayfinding-five-rows', 'wayfinding-four-rows', 'wayfinding-seven-rows', 'wayfinding-six-rows', 'wayfinding-three-rows'],
    'mso_templates': ['mso-five-sections', 'mso-four-sections'],
    'mbu_templates': ['mbu-three-split'],
    'itk_templates': ['itk-three-split']
  },
  'slides': {
    'aarhus_templates': ['rss-aarhus'],
    'default_templates': ['ik-iframe', 'manual-calendar', 'only-image', 'only-video', 'portrait-text-top', 'rss-default', 'text-bottom', 'text-left', 'text-right', 'text-top', 'slideshow'],
    'ding2': ['ding-events', 'opening-hours'],
    'dokk1_templates': ['dokk1-info', 'dokk1-multiple-calendar', 'dokk1-single-calendar', 'wayfinding', 'dokk1-coming-events', 'dokk1-text-and-image'],
    'mso_templates': ['event-calendar', 'header-top', 'mso-iframe'],
    'mbu_templates': ['mbu-header', 'mbu-footer']
  }
};


/**
 * Process SCSS using libsass
 */
gulp.task('sassTemplates', 'Compile the sass for each templates into minified css files.', function () {
  'use strict';

  // Iterates through the screen and slide templates defined in templates variable, and compresses each one.
  for (var templateType in templates) {
    if (templates.hasOwnProperty(templateType)) {
      for (var folder in templates[templateType]) {
        if (templates[templateType].hasOwnProperty(folder)) {
          var arr = templates[templateType][folder];

          arr.forEach(function (element) {
            var path = templatesPath + folder + '/' + templateType + '/' + element + '/';

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
    }
  }
});
