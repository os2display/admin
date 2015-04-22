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

    'itkLog',

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
  function () {
  }
).config(function ($provide) {
    "use strict";

    $provide.decorator("$exceptionHandler", ['$delegate', '$injector', function ($delegate, $injector) {
      return function (exception, cause) {
        $delegate(exception, cause);

        $injector.get('itkLogFactory').error(exception, cause);
      };
    }]);
  }
);
