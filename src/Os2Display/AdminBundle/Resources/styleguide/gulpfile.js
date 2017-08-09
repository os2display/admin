#!/usr/bin/env node

// Plugin to handle parameters.
var argv = require('yargs')
.alias('t', 'theme')
.default('theme', ['os2display'])
  .argv;

// Gulp basic.
var gulp = require('gulp-help')(require('gulp'), {
  'afterPrintCallback': function () {
    var args = [];
  }
});

// Plugins.
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var stylelint = require('gulp-stylelint');

/**
 * Configuration object.
 */
var configuration = {
  // Base theme.
  'os2display': {
    "sass": {
      "paths": ['./source/**/*.scss'],
      "srcs": ['./source/*.scss'],
      'dest': './source/css'
    },
  }
};

/**
 * Setup task for sass compilation.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function sassTask(theme, config) {
  var taskName = 'sass_' + theme;

  // Process SCSS.
  gulp.task(taskName, false, function () {

    var pipe = gulp.src(config.sass.srcs)
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: [
        'scss/assets/compass-mixins/lib'
      ]
    }).on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(config.sass.dest));
  });

  return taskName;
}

/**
 * Setup stylelint tasks.
 *
 * Note: Because stylelint task is async it returns it's gulp process.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function stylelintTask(theme, config) {
  var taskName = 'stylelint_' + theme;

  gulp.task(taskName, false, function lintCssTask() {
    return gulp.src(config.sass.paths)
    .pipe(stylelint({
      reporters: [
        {formatter: 'string', console: true}
      ]
    }));
  });

  return taskName;
}

/**
 * Watch sass and stylelint tasks.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function watchTasks(theme, config) {
  var taskName = 'watch_' + theme;

  gulp.task(taskName, false, function () {
    gulp.watch(config.sass.paths, ['sass']);
    gulp.watch(config.sass.paths, ['stylelint']);

    if (config.hasOwnProperty('js')) {
      gulp.watch(config.js.paths, ['eslint']);
    }
  });

  return taskName;
}

/**
 * Dynamically setup tasks base on the selected theme.
 *
 * @param themes
 *   Theme name as an string array. Used as index in the
 *   configuration pobject.
 */
function setupTasks(themes) {
  // Define task arrays.
  var sassTaskNames = [];
  var stylelintTaskNames = [];
  var jsMinifyTaskNames = [];
  var watchTasksNames = [];

  // Ensure themes is an array and if not convert it.
  if (Object.prototype.toString.call(themes) !== '[object Array]') {
    themes = [themes];
  }


  // Loop over the selected themes.
  for (var i in themes) {
    var theme = themes[i];
    var config = configuration[theme];

    // SASS tasks.
    sassTaskNames.push(sassTask(theme, config));

    // Style-lint tasks.
    stylelintTaskNames.push(stylelintTask(theme, config));

    // Watch tasks.
    watchTasksNames.push(watchTasks(theme, config));
  }

  // Define tasks.
  gulp.task('sass', 'Compile SCSS into CSS', sassTaskNames);
  gulp.task('stylelint', 'Use style-lint to check SCSS (using stylelintrc.json rules)', stylelintTaskNames);
  gulp.task('watch', 'Watch for changes in files', watchTasksNames);

  // Default task;
  gulp.task('default', ['sass', 'stylelint']);
}

/**
 * ------- Run task's -------
 */
setupTasks(argv.theme);