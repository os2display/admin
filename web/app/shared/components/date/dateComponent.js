/**
 * @file
 * Contains the itkDateComponent module.
 */

/**
 * Setup the module.
 *
 * Requires
 *   moment.js
 */
(function () {
  'use strict';

  var app;
  app = angular.module("itkDateComponent", []);

  /**
   * date component directive.
   *
   * html parameters:
   */
  app.directive('dateComponent', ['$interval',
    function ($interval) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'app/shared/components/date/date.html',
        scope: {
        },
        link: function (scope) {
          // Setup danish localization.
          moment.locale('da', {
            weekdays : [
              "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag", "Søndag", "Mandag"
            ],
            months: [
              "Januar", "Februar", "Marts", "April", "Maj", "Juni",
              "Juli", "August", "September", "Oktober", "November", "December"
            ]
          });

          // Get current datetime.
          scope.date = moment();

          $interval(function() {
            // Update current datetime.
            scope.date = moment();
          }, 10000);
        }
      };
    }
  ]);
}).call(this);
