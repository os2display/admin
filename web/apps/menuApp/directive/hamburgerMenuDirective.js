/**
 * Hamburger menu directive.
 */
angular.module('menuApp')
  .directive('hamburgerMenu', ['$http', '$location', 'busService',
    function ($http, $location, busService) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: '/apps/menuApp/directive/hamburger-menu.html?' + config.version,
        scope: {},
        link: function (scope) {
          scope.hamburgerMenuItems = [];
          scope.menuOpen = false;
          scope.currentUser = null;
          scope.url = $location.url();

          // Listen for Main menu items.
          busService.$on('userService.returnCurrentUser', function returnCurrentUser(event, user) {
            scope.$apply(function () {
              scope.currentUser = user;
            });
          });

          // Request user
          busService.$emit('userService.getCurrentUser', {});

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

          // Listen for location change
          busService.$on('$locationChangeSuccess', function () {
            scope.url = $location.url();
          });

          // Request user
          busService.$emit('userService.getCurrentUser', {});

          /**
           * Toggle hamburger menu.
           */
          scope.toggleMenu = function () {
            scope.menuOpen = !scope.menuOpen;

            if (scope.menuOpen) {
              busService.$emit('bodyService.addClass', 'is-locked');
            }
            else {
              busService.$emit('bodyService.removeClass', 'is-locked');
            }


            $('.hamburger-menu').click(
              function(e) {
                e.stopPropagation();
              }
            );
          };

          scope.userHasPermission = function userHasPermission(permission) {
            if (!permission) {
              return true;
            }
            if (permission === 'super-admin' && scope.currentUser && scope.currentUser.is_super_admin) {
              return true;
            }
            else if (permission === 'admin' && scope.currentUser && scope.currentUser.is_admin) {
              return true;
            }
            return false;
          };

          /**
           * Update templates.
           * @TODO: Move to other location
           */
          scope.updateTemplates = function updateTemplates() {
            scope.menuOpen = false;

            $http.get('/api/command/update_templates')
              .success(function(data, status, headers, config) {
                busService.$emit('log.info', {
                  'msg': 'Templates opdateret.',
                  'timeout': 3000
                });
              })
              .error(function(data, status, headers, config) {
                busService.$emit('log.error', {
                  'cause': status,
                  'msg': 'Update af templates fejlede.'
                });
              });
          };

          /**
           * Reindex search.
           * @TODO: Move to other location
           */
          scope.reindex = function reindex() {
            scope.menuOpen = false;

            $http.get('/api/command/reindex')
              .success(function(data, status, headers, config) {
                busService.$emit('log.info', {
                  'msg': 'Reindex gennemført.',
                  'timeout': 3000
                });
              })
              .error(function(data, status, headers, config) {
                busService.$emit('log.error', {
                  'cause': status,
                  'msg': 'Reindex fejlede.'
                });
              });
          };

          /**
           * Force push.
           * @TODO: Move to other location
           */
          scope.forcePush = function reindex() {
            scope.menuOpen = false;

            $http.get('/api/command/forcepush')
              .success(function(data, status, headers, config) {
                busService.$emit('log.info', {
                  'msg': 'Force push gennemført.',
                  'timeout': 3000
                });
              })
              .error(function(data, status, headers, config) {
                busService.$emit('log.error', {
                  'cause': status,
                  'msg': 'Force push fejlede.'
                });
              });
          };
        }
      };
    }
  ]);