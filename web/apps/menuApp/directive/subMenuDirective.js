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
          scope.subMenuItems = [];

          // Listen for Main menu items.
          busService.$on('menuApp.returnSubMenuItems', function returnSubMenuItems(event, items) {
            // Add items received.
            items.forEach(function(element) {
              scope.subMenuItems.push(element);
            });

            // Sort by weight.
            scope.subMenuItems.sort(function(a, b) {
              return parseInt(a.weight) - parseInt(b.weight);
            });
          });

          /**
           * Set the submenu items according to what the url starts with.
           *
           * @TODO: Make this event based!
           */
          var updateSubMenu = function () {
            if (url.indexOf('/channel') === 0 || url.indexOf('/shared-channel') === 0) {
              scope.subMenuItems = [
                {
                  title: 'Oversigt',
                  path: '/#/channel-overview',
                  classSuffix: 'overview'
                },
                {
                  title: 'Opret kanal',
                  path: '/#/channel',
                  classSuffix: 'create-channel'
                }
              ];

              if (window.config.sharingService.enabled) {
                scope.subMenuItems.push(
                  {
                    title: 'Delte kanaler',
                    path: '/#/shared-channel-overview',
                    classSuffix: 'overview'
                  }
                );
              }
            }
            else if (url.indexOf('/slide') === 0) {
              scope.subMenuItems = [
                {
                  title: 'Oversigt',
                  path: '/#/slide-overview',
                  classSuffix: 'overview'
                },
                {
                  title: 'Opret slide',
                  path: '/#/slide',
                  classSuffix: 'create-channel'
                }
              ];
            }
            else if (url.indexOf('/screen') === 0) {
              scope.subMenuItems = [
                {
                  title: 'Oversigt',
                  path: '/#/screen-overview',
                  classSuffix: 'overview'
                },
                {
                  title: 'Opret skærm',
                  path: '/#/screen',
                  classSuffix: 'create-channel'
                }
              ];
            }
            else if (url.indexOf('/template') === 0) {
              scope.subMenuItems = [
                {
                  title: 'Oversigt',
                  path: '/#/template-overview',
                  classSuffix: 'overview'
                },
                {
                  title: 'Opret skabelon',
                  path: '/#/template',
                  classSuffix: 'create-channel'
                }
              ];
            }
            else if (url.indexOf('/media') === 0) {
              scope.subMenuItems = [
                {
                  title: 'Oversigt',
                  path: '/#/media-overview',
                  classSuffix: 'overview'
                },
                {
                  title: 'Upload medie',
                  path: '/#/media/upload',
                  classSuffix: 'create-media'
                }
              ];
            }
            else {
              scope.subMenuItems = [];
            }
          };
          updateSubMenu();

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