/**
 * Main menu directive.
 */
angular.module('menuApp')
  .directive('mainMenu', ['$location', 'busService',
    function ($location, busService) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: '/apps/menuApp/directive/main-menu.html?' + config.version,
        scope: {},
        link: function (scope) {
          scope.mainMenuItems = [];
          scope.siteTitle = window.config.siteTitle;
          scope.url = $location.url();

          // Listen for Main menu items.
          busService.$on('menuApp.returnMainMenuItems', function returnMainMenuItems(event, items) {
            // Add items received.
            items.forEach(function(element) {
              scope.mainMenuItems.push(element);
            });

            // Sort by weight.
            scope.mainMenuItems.sort(function(a, b) {
              return parseInt(a.weight) - parseInt(b.weight);
            });
          });

          // Listen for location change
          busService.$on('$locationChangeSuccess', function () {
            scope.url = $location.url();
          });

          // Request Main menu items
          busService.$emit('menuApp.requestMainMenuItems', {});
        }
      };
    }
  ]);