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
    'itkSharedChannelPickerWidget',
    'itkChannelRemoverWidget',
    'itkRegionPreviewWidget',
    'itkDateComponent'
  ],
  function() {}
).config(function($provide) {
    'use strict';

    // Install raven.
    Raven.config('https://de9fc538a66e44c4a793fed8e5d39878@app.getsentry.com/41300', {
      // pass along the version of your application
      release: '3.0.0-aplha'

      // we highly recommend restricting exceptions to a domain in order to filter out clutter
      //whitelistUrls: ['example.com/scripts/']
    }).install();


    $provide.decorator("$exceptionHandler", ['$delegate', function ($delegate) {
      return function (exception, cause) {
        $delegate(exception, cause);
        Raven.captureException(exception);
      };
    }]);
  });
