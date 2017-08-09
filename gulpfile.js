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
