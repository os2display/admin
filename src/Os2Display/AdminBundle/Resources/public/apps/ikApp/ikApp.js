/**
 * @file
 * Contains the ikApp module.
 */

/**
 * ikApp - Main entry point for the app.
 *
 * Register modules here.
 */
angular.module('ikApp').config([
  '$sceDelegateProvider', '$translateProvider', function ($sceDelegateProvider, $translateProvider) {
    'use strict';

    // Set up translations.
    $translateProvider
    .useSanitizeValueStrategy('escape')
    .useStaticFilesLoader({
      prefix: 'bundles/os2displayadmin/apps/ikApp/translations/locale-',
      suffix: '.json'
    })
    .preferredLanguage('da')
    .fallbackLanguage('da')
    .forceAsyncReload(true);

    // The administration interface and the client code do not run on the same
    // domain/sub-domain hence we need to whitelist the domains to load slide
    // templates and CSS form the administration domain.
    $sceDelegateProvider.resourceUrlWhitelist([
      // Allow same origin resource loads.
      'self',
      // Allow loading from outer templates domain.
      '**'
    ]);
  }
]);
