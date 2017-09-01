var gulp = require('gulp-help')(require('gulp'));
var sass = require('gulp-sass');

var templatesPath = 'Resources/public/templates/';

// @TODO: Discover this structure automatically
var templates = {
  'aarhus-templates': {
    'slides': [
      'event-calendar',
      'header-top',
      'rss-aarhus'
    ]
  },
  'dokk1-templates': {
    'screens': [
      'dokk1-two-rows-variable',
      'wayfinding-eleven-rows',
      'wayfinding-five-rows',
      'wayfinding-four-rows',
      'wayfinding-seven-rows',
      'wayfinding-six-rows',
      'wayfinding-three-rows'
    ],
    'slides': [
      'dokk1-coming-events',
      'dokk1-info',
      'dokk1-multiple-calendar',
      'dokk1-single-calendar',
      'only-image-vertical',
      'wayfinding'
    ]
  },
  'itk-templates': {
    'screens': [
      'itk-three-split'
    ]
  },
  'mbu-templates': {
    'screens': [
      'mbu-three-split'
    ],
    'slides': [
      'mbu-footer',
      'mbu-header'
    ]
  },
  'mso-templates': {
    'screens': [
      'mso-five-sections',
      'mso-four-sections'
    ],
    'slides': [
      'mso-iframe'
    ]
  }
};

/**
 * Process SCSS using libsass
 */
gulp.task('sass', 'Compile the sass for each templates into minified css files.', function () {
  'use strict';

  for (var organization in templates) {
    for (var templateType in templates[organization]) {
      for (var folder in templates[organization][templateType]) {
        folder = templates[organization][templateType][folder];

        var path = templatesPath + organization + '/' + templateType + '/' + folder + '/';

        gulp.src(path + folder + '.scss')
        .pipe(sass({
          outputStyle: 'compressed',
          includePaths: [
            '../../Os2Display/DefaultTemplateBundle/Resources/sass/compass-mixins/lib'
          ]
        }).on('error', sass.logError))
        .pipe(gulp.dest(path));
      }
    }
  }

});
