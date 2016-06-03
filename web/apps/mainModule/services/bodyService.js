/**
 * Body service.
 *
 * Service to modify body element
 */
angular.module('mainModule').service('bodyService', ['busService', '$location',
  function (busService, $location) {
    'use strict';
    var classesAdded = [];

    // Listen for location change
    //   Remove added classes from body
    busService.$on('$locationChangeSuccess', function () {
      for (var classAdded in classesAdded) {
        angular.element('body').removeClass(classesAdded[classAdded]);
      }
      classesAdded = [];
    });

    /**
     * Listen for bodyService.addClass events.
     *
     * Add the class to body element.
     */
    busService.$on('bodyService.addClass', function addClass(event, args) {
      angular.element('body').addClass(args);
      classesAdded.push(args);
    });
  }
]);
