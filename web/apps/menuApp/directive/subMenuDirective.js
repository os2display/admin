/**
 * Sub menu directive.
 */
angular.module('menuApp')
  .directive('subMenu', ['$location', 'busService',
    function ($location, busService) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: '/apps/menuApp/directive/sub-menu.html?' + config.version,
        scope: {},
        link: function (scope) {
          var url = $location.url();
          var subMenus = [];
          scope.subMenuItems = [];

          // Listen for Sub menu items.
          // Add all not already added menu items.
          busService.$on('menuApp.returnSubMenuItems', function returnSubMenuItems(event, data) {
            for (var i = 0; i < data.length; i++) {
              if (subMenus.indexOf(data[i].mainMenuItem) === -1) {
                subMenus[data[i].mainMenuItem] = [];
              }

              for (var j = 0; j < data[i].items.length; j++) {
                var item = data[i].items[j];

                var add = true;
                for (var k = 0; k < subMenus[data[i].mainMenuItem].length; k++) {
                  if (subMenus[data[i].mainMenuItem][k].title === item.title) {
                    add = false;
                    break;
                  }
                }

                if (add) {
                  subMenus[data[i].mainMenuItem].push(item);
                }
              }
            }

            // Sort by weight.
            for (var key in subMenus) {
              subMenus[key].sort(function(a, b) {
                return parseInt(a.weight) - parseInt(b.weight);
              });
            }

            updateSubMenu();
          });

          /**
           * Set the submenu items according to what the url starts with.
           */
          var updateSubMenu = function () {
            if (url.indexOf('/channel') === 0 || url.indexOf('/shared-channel') === 0) {
              scope.subMenuItems = subMenus['channel'];
            }
            else if (url.indexOf('/slide') === 0) {
              scope.subMenuItems = subMenus['slide'];
            }
            else if (url.indexOf('/screen') === 0) {
              scope.subMenuItems = subMenus['screen'];
            }
            else if (url.indexOf('/media') === 0) {
              scope.subMenuItems = subMenus['media'];
            }
            else {
              scope.subMenuItems = [];
            }
          };

          // Listen for location change
          busService.$on('$locationChangeSuccess', function () {
            url = $location.url();
            updateSubMenu();
          });

          // Request Sub menu items
          busService.$emit('menuApp.requestSubMenuItems');
        }
      };
    }
  ]);