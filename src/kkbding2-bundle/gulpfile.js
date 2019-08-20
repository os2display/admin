/* jshint node: true */
"use strict";

const fs = require('fs');
const del = require("del");
const gulp = require("gulp");
const concat = require("gulp-concat");
const uglify = require("gulp-uglify");
const rename = require("gulp-rename");
const sass = require('gulp-sass');

/**
 * Get an array of dir names in a dir.
 *
 * @param {string} source The directory to find directory names in.
 * @returns {string[]} Array of strings with directory names.
 */
const dirsInDir = source => fs.readdirSync(source, {withFileTypes: true})
  .filter(c => fs.statSync(source + '/' + c).isDirectory());

const slidesPath = "Resources/public/templates/ding2/slides";
const screensPath = "Resources/public/templates/ding2/screens";
const toolsDir = "Resources/public/apps/dingEditors";
const distToolsMinifiedJs = "Resources/public/assets/build/";

/**
 * Delete the generated minified files.
 */
function clean() {
  return del([
    `${distToolsMinifiedJs}/*.min.js`  ,
    `${slidesPath}/**/*.css`,
    `${screensPath}/**/*.css`,
  ]);
}

/**
 * Compile the JS for the tools used in the admin interface.
 */
const compileToolsJs = () => {
  return gulp.src(`${toolsDir}/*.js`)
    .pipe(concat('kkbding2integration.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest(distToolsMinifiedJs));
};

/**
 * Compile all .scss files in the slides and screen folders.
 */
const compileScss = () => {
  dirsInDir(slidesPath).map(function (item) {
    const fileName = item.split("/").pop() + ".scss";
    gulp.src(`${slidesPath}/${item}/${fileName}`)
      .pipe(sass({
        outputStyle: 'compressed'
      }).on('error', sass.logError))
      .pipe(rename({extname: ".css"}))
      .pipe(gulp.dest(`${slidesPath}/${item}`));
  });

  dirsInDir(screensPath).map(function (item) {
    const fileName = item.split("/").pop() + ".scss";
    gulp.src(`${screensPath}/${item}/${fileName}`)
      .pipe(sass({
        outputStyle: 'compressed',
        includePaths: [
          // Include compass from the default template bundle instead of
          // shipping with our own.
          '../../vendor/os2display/default-template-bundle/Resources/sass/compass-mixins/lib'
        ]
      }).on('error', sass.logError))
      .pipe(rename({extname: ".css"}))
      .pipe(gulp.dest(`${screensPath}/${item}`));
  });

  return new Promise(function (resolve) {
    resolve();
  });
};

/**
 * Watch for changes to JS and CSS.
 */
function watchChanges() {
  gulp.watch(`${toolsDir}/*.js`, compileToolsJs);
  gulp.watch(`${slidesPath}/**/*.scss`, compileScss);
}

const compile = gulp.parallel(compileToolsJs, compileScss);
compile.description = 'Compile all';

const all = gulp.series(clean, compile);
all.description = 'Clean and compile all';

const watch = gulp.series(clean, compile, watchChanges);

exports.watch = watch;
exports.compile = compile;
exports.all = all;

exports.default = all;
