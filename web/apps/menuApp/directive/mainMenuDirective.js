/**
 * Main menu directive.
 */
angular.module('menuApp')
  .directive('mainMenu', ['busService',
    function (busService) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: '/apps/menuApp/directive/main-menu.html?' + config.version,
        scope: {},
        link: function (scope) {
          scope.mainMenuItems = [];
          scope.siteTitle = window.config.siteTitle;

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

          // Request Main menu items
          busService.$emit('menuApp.requestMainMenuItems', {});
        }
      };
    }
  ]);