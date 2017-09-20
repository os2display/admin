/**
 * @file
 * Screen overview directive.
 */

/**
 * Directive to show the Screen overview.
 */
angular.module('ikApp').directive('ikScreenOverview', [
  'busService',
  function(busService) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikSelectedChannels: '=',
        ikOverlay: '@'
      },
      controller: function($scope, $filter, $controller, screenFactory, userService, busService) {
        $controller('BaseSearchController', {$scope: $scope});

        // Get filter selection "all/mine" from localStorage.
        $scope.showFromUser = localStorage.getItem('overview.media.search_filter_default') ?
          localStorage.getItem('overview.media.search_filter_default') :
          'all';

        // Screens to display.
        $scope.screens = [];

        /**
         * Updates the screens array by send a search request.
         */
        $scope.updateSearch = function() {
          // Get search text from scope.
          $scope.baseQuery.text = $scope.search_text;

          $scope.loading = true;

          screenFactory.searchScreens($scope.baseQuery).then(
            function(data) {
              // Total hits.
              $scope.hits = data.hits;

              // Extract search ids.
              var ids = [];
              for (var i = 0; i < data.results.length; i++) {
                ids.push(data.results[i].id);
              }

              // Load slides bulk.
              screenFactory.loadScreensBulk(ids).then(
                function (data) {
                  $scope.screens = data;

                  $scope.pager.page = 0;

                  $scope.loading = false;
                },
                function (reason) {
                  busService.$emit('log.error', {
                    'cause': reason,
                    'msg': 'Kunne ikke hente søgeresultater.'
                  });
                  $scope.loading = false;
                }
              );
            }
          );
        };

        /**
         * Update search result on screen deletion.
         */
        $scope.$on('screen-deleted', function() {
          $scope.updateSearch();
        });

        /**
         * Updates the search filter based on current orientation and user
         */
        $scope.setSearchFilters = function setSearchFilters() {
          delete $scope.baseQuery.filter;

          // No groups selected and "all" selected => select all groups and my.
          var selectedGroupIds = $filter('filter')($scope.userGroups, { selected: true }, true).map(function (group) {
            return group.id;
          });

          $scope.baseQuery.filter = $scope.baseBuildSearchFilter(selectedGroupIds);

          $scope.updateSearch();
        };

        $scope.setSearchFilters();
      },
      templateUrl: '/apps/ikApp/shared/elements/screenOverview/screen-overview-directive.html?' + window.config.version
    };
  }
]);
