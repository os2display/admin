/**
 * Sub menu directive.
 */
angular.module('menuApp')
  .directive('subMenu', ['$location', 'busService',
    function ($location, busService) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: 'bundles/os2displayadmin/apps/menuApp/directive/sub-menu.html?' + config.version,
        scope: {},
        link: function (scope) {
          scope.url = $location.url();
          var subMenus = [];
          scope.subMenuItems = [];

          // Listen for Sub menu items.
          // Add all not already added menu items.
          busService.$on('menuApp.returnSubMenuItems', function returnSubMenuItems(event, data) {
            for (var i = 0; i < data.length; i++) {
              var mainMenuItem = data[i].mainMenuItem;
              var items = data[i].items;

              // If mainMenuItem is not already there create it
              if (!subMenus.hasOwnProperty(mainMenuItem)) {
                subMenus[mainMenuItem] = [];
              }

              // Iterate each item.
              // Make sure they have not already been added to sub menu,
              //   else add it.
              for (var j = 0; j < items.length; j++) {
                var item = items[j];

                var add = true;
                for (var k = 0; k < subMenus[mainMenuItem].length; k++) {
                  if (subMenus[mainMenuItem][k].title === item.title) {
                    add = false;
                    break;
                  }
                }

                if (add) {
                  subMenus[mainMenuItem].push(item);
                }
              }
            }

            // Sort by weight.
            for (var key in subMenus) {
              subMenus[key].sort(function(a, b) {
                return parseInt(a.weight) - parseInt(b.weight);
              });
            }

            // Update what submenu is displayed.
            updateSubMenu();
          });

          /**
           * Set the submenu items according to what the url starts with.
           * @TODO: Make this generic.
           */
          var updateSubMenu = function () {
            if (scope.url.indexOf('/channel') === 0 || scope.url.indexOf('/shared-channel') === 0) {
              scope.subMenuItems = subMenus['channel'];
            }
            else if (scope.url.indexOf('/slide') === 0) {
              scope.subMenuItems = subMenus['slide'];
            }
            else if (scope.url.indexOf('/screen') === 0) {
              scope.subMenuItems = subMenus['screen'];
            }
            else if (scope.url.indexOf('/media') === 0) {
              scope.subMenuItems = subMenus['media'];
            }
            else if (scope.url.indexOf('/admin') === 0) {
              scope.subMenuItems = subMenus['admin'];
            }
            else {
              scope.subMenuItems = [];
            }
          };

          // Listen for location change
          busService.$on('$locationChangeSuccess', function () {
            scope.url = $location.url();

            updateSubMenu();
          });

          // Request Sub menu items
          busService.$emit('menuApp.requestSubMenuItems');
        }
      };
    }
  ]);
