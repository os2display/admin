/**
 * @file
 * Contains the ikApp module.
 */

/**
 * ikApp - Main entry point for the app.
 * @type {*}
 */
angular.module('ikApp', [
    'ngRoute',
    'ngAnimate',
    'angularFileUpload',
    'checklist-model',
    'colorpicker.module',
    'datetimePicker',
    'ngLocale',
    'taiPlaceholder',
    'ngModal',
    'angular.css.injector',

    'itkControlPanel',
    'itkScreenTemplatePickerWidget',
    'itkTextWidget',
    'itkTextAreaWidget',
    'itkNumberWidget',
    'itkChannelPickerWidget'
  ],
  function() {}
);
