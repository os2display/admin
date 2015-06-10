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
)
  .config(function ($sceDelegateProvider) {
    'use strict';

    // The administration interface and the client code do not run on the same
    // domain/sub-domain hence we need to whitelist the domains to load slide
    // templates and CSS form the administration domain.
    $sceDelegateProvider.resourceUrlWhitelist([
      // Allow same origin resource loads.
      'self',
      // Allow loading from outer templates domain.
      '**'
    ]);
  })
  .config(function ($provide) {
    "use strict";

    $provide.decorator("$exceptionHandler", ['$delegate', '$injector',
      function ($delegate, $injector) {
        return function (exception, cause) {
          $delegate(exception, cause);

          $injector.get('itkLog').error(exception, cause);
        };
      }
    ]);
  });
