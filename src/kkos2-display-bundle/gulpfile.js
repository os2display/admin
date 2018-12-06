'use strict';

const gulp = require('gulp-help')(require('gulp'));

// Plugins.
const jshint = require('gulp-jshint');
const stylish = require('jshint-stylish');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const ngAnnotate = require('gulp-ng-annotate');
const rename = require('gulp-rename');
const yaml = require('js-yaml');
const fs = require('fs');
const header = require('gulp-header');

const jsBuildDir = 'Resources/public/assets/build';
const templatesPath = 'Resources/public/templates/';

// Get information for top of minified files.
const pkg = require('./version.json');
const banner = [
  '/**',
  ' * @name <%= pkg.name %>',
  ' * @version v<%= pkg.version %>',
  ' * @link <%= pkg.link %>',
  ' */',
  ''
].join('\n');


const slideFolders = [
  'slides/kk-events',
  'slides/kk-color-messages',
];

gulp.task('sass', 'Compile each of the scss files into a compressed css file.', function () {
    slideFolders.map(function (item) {
      var path = templatesPath + item;
      gulp.src(path + '/' + item.split('/').pop() + '.scss')
        .pipe(sass({
          outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(gulp.dest(path));
    });
  }
);

// We only want to process our own non-processed JavaScript files.
var adminJsPath = (function () {
  var configFiles = [
      'Resources/config/angular.yml'
    ],
    jsFiles = [],

    // Breadth-first descend into data to find "files".
    buildFiles = function (data) {
      if (typeof (data) === 'object') {
        for (var p in data) {

          if (p === 'files') {
            jsFiles = jsFiles.concat(data[p]);
          } else {
            buildFiles(data[p]);
          }
        }
      }
    };

  configFiles.forEach(function (path) {
    var data = yaml.safeLoad(fs.readFileSync(path, 'utf8'));
    buildFiles(data);
  });

  return jsFiles.map(function (file) {
    return 'Resources/public/' + file.split('bundles/kkos2displayintegration/')[1];
  });
}());

/**
 * Run Javascript through JSHint.
 */
gulp.task('jshint', 'Runs JSHint on js', function () {
  return gulp.src(adminJsPath)
    .pipe(jshint())
    .pipe(jshint.reporter(stylish));
});

/**
 * Build single admin app.js file.
 */
gulp.task('js', 'Build all custom js files into one minified js file.', function () {
    return gulp.src(adminJsPath)
      .pipe(concat('kkos2displayintegration'))
      .pipe(ngAnnotate())
      .pipe(uglify())
      .pipe(rename({extname: ".min.js"}))
      .pipe(header(banner, {pkg: pkg}))
      .pipe(gulp.dest(jsBuildDir));
  }
);

gulp.task('js-frontend', 'Build JS for the frontend.', function () {
    slideFolders.map(function (item) {
      const path = templatesPath + item;
      gulp.src(path + '/' + item.split('/').pop() + '.js')
        .pipe(uglify())
        .pipe(rename({extname: ".min.js"}))
        .pipe(gulp.dest(jsBuildDir));
    });
  }
);

/**
 * Build single app.js file.
 */
gulp.task('js-src', 'Report all source files for "js" task.', function () {
  adminJsPath.forEach(function (path) {
    console.log(path + '\n');
  });
});

gulp.task('js:watch', function () {
  gulp.watch(['gulpfile.js', adminJsPath], ['js']);
});

gulp.task('sass:watch', function () {
  gulp.watch('Resources/public/templates/**/*.scss', ['sass']);
});

gulp.task('js-frontend:watch', function () {
  gulp.watch('Resources/public/templates/**/*.js', ['js-frontend']);
});
