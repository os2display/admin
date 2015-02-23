/**
 * @file
 * Contains the ikApp module.
 */

/**
 * ikApp - Main entry point for the app.
 *
 * Register modules here.
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
    'itkChannelPickerWidget',
    'itkChannelRemoverWidget',
    'itkRegionPreviewWidget'
  ],
  function() {}
);
