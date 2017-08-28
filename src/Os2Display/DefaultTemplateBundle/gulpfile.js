var gulp = require('gulp-help')(require('gulp'));
var jshint = require('gulp-jshint');
var stylish = require('jshint-stylish');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var yaml = require('js-yaml');
var fs = require('fs');
var header = require('gulp-header');

var templatesPath = 'Resources/public/templates/';

// @TODO: Discover this structure automatically
var templates = {
  'screens': {
    'default_templates': [
      'five-sections',
      'three-columns',
      'two-columns'
    ]
  },
  'slides': {
    'default_templates': [
      'ik-iframe',
      'manual-calendar',
      'only-image',
      'only-video',
      'portrait-text-top',
      'rss-default',
      'text-bottom',
      'text-left',
      'text-right',
      'text-top',
      'slideshow'
    ]
  }
};

/**
 * Process SCSS using libsass
 */
gulp.task('sass', 'Compile the sass for each templates into minified css files.', function () {
  'use strict';

  // Iterates through the screen and slide templates defined in templates variable, and compresses each one.
  for (var templateType in templates) {
    for (var folder in templates[templateType]) {
      var arr = templates[templateType][folder];

      arr.forEach(function (element) {
        var path = templatesPath + folder + '/' + templateType + '/' + element + '/';

        gulp.src(path + element + '.scss')
        .pipe(sass({
          outputStyle: 'compressed',
          includePaths: [
            'Resources/sass/compass-mixins/lib'
          ]
        }).on('error', sass.logError))
        .pipe(gulp.dest(path));
      });
    }
  }
});
