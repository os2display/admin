/**
 * @file
 * Contains base search controller.
 */

angular.module('ikApp').controller('BaseSearchController', [
  '$scope', 'userService', 'busService',
  function ($scope, userService, busService) {
    'use strict';

    // Get current user.
    $scope.currentUser = userService.getCurrentUser();

    // Get current user groups.
    $scope.userGroups = [];
    var cleanupGetCurrentUserGroups = busService.$on('channelController.getCurrentUserGroups', function (event, result) {
      if (result.error) {
        $scope.setSearchFilters();
        return;
      }

      $scope.$apply(
        function () {
          $scope.userGroups = result;

          // Updated search filters (build "mine" filter with user id). It
          // will trigger an search update.
          $scope.setSearchFilters();
        }
      );
    });
    userService.getCurrentUserGroups('channelController.getCurrentUserGroups');

    /**
     * Build search filter based on selected groups.
     *
     * @param {array} selectedGroupIds
     *   The currently selected group id's.
     *
     * @returns {{query: {bool: {must: Array, should: Array}}}}
     *   The filter to send to search.
     */
    $scope.baseBuildSearchFilter = function buildSearchFilter(selectedGroupIds) {
      var filter = {
        "query": {
          "bool": {
            "must": [],
            "should": []
          }
        }
      };

      // Filter based on user selection (all or me).
      if ($scope.showFromUser !== 'all') {
        if ($scope.currentUser) {
          var term = {};
          term.term = {
            user: $scope.currentUser.id
          };
          filter.query.bool.must.push(term);
        }
      }

      if ($scope.showFromUser === 'all' && selectedGroupIds.length === 0) {
        // Find all allowed group id's.
        selectedGroupIds = $scope.userGroups.map(function (group) {
          return group.id;
        });

        // Add all "my" to the query.
        filter.query.bool.should.push({
          "bool": {
            "must": [
              {
                "term": {
                  "user": $scope.currentUser.id
                }
              }
            ]
          }
        });
      }

      if (selectedGroupIds.length) {
        // Select if it should be "or" or "and" between "user" and "groups".
        var type = $scope.showFromUser === 'all' ? "should" : "must";
        filter.query.bool[type].push({
          "bool": {
            "must": [
              {
                "terms": {
                  "groups": selectedGroupIds
                }
              }
            ]
          }
        });
      }

      return filter;
    };

    /**
     * Clean up registered listeners.
     */
    $scope.$on('$destroy', function () {
      cleanupGetCurrentUserGroups();
    });
  }
]);
