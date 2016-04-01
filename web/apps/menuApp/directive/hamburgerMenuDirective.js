/**
 * Hamburger menu directive.
 */
angular.module('menuApp')
  .directive('hamburgerMenu', ['busService',
    function (busService) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: '/apps/menuApp/directive/hamburger-menu.html?' + config.version,
        scope: {},
        link: function (scope) {
          scope.hamburgerMenuItems = [];
          scope.menuOpen = false;
          scope.currentUser = null;

          // Listen for Main menu items.
          busService.$on('mainApp.returnUser', function returnMainMenuItems(event, user) {
            scope.currentUser = user;
          });

          // Request user
          busService.$emit('main.requestUser', {});

          // Listen for Hamburger menu items.
          busService.$on('menuApp.returnHamburgerMenuItems', function returnHamburgerMenuItems(event, items) {
            // Add items received.
            items.forEach(function(element) {
              scope.hamburgerMenuItems.push(element);
            });

            // Sort by weight.
            scope.hamburgerMenuItems.sort(function(a, b) {
              return parseInt(a.weight) - parseInt(b.weight);
            });
          });

          // Request Hamburger menu items
          busService.$emit('menuApp.requestHamburgerMenuItems', {});

          /**
           * Toggle hamburger menu.
           */
          scope.toggleMenu = function () {
            scope.menuOpen = !scope.menuOpen;
            $('html').toggleClass('is-locked');
          };
        }
      };
    }
  ]);