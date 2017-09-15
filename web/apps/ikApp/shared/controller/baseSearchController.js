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

    $scope.loading = false;

    // Set default orientation and sort.
    $scope.sort = {"created_at": "desc"};

    // Set default search text.
    $scope.search_text = '';

    // Default pager values.
    $scope.pager = {
      "size": 6,
      "page": 0
    };
    $scope.hits = 0;

    // Setup default search options.
    $scope.baseQuery = {
      "fields": ['name', 'title'],
      "text": '',
      "sort": {
        "created_at": {
          "order": "desc"
        }
      },
      'pager': $scope.pager,
      "filter": {}
    };

    // Get current user groups.
    $scope.userGroups = $scope.currentUser.groups;

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
     * Changes the sort order and updated the screens.
     *
     * @param sort_field
     *   Field to sort on.
     * @param sort_order
     *   The order to sort in 'desc' or 'asc'.
     */
    $scope.setSort = function(sort_field, sort_order) {
      // Only update search if sort have changed.
      if ($scope.sort[sort_field] === undefined || $scope.sort[sort_field] !== sort_order) {

        // Update the store sort order.
        $scope.sort = { };
        $scope.sort[sort_field] = sort_order;

        // Update the search variable.
        $scope.baseQuery.sort = { };
        $scope.baseQuery.sort[sort_field] = {
          "order": sort_order
        };

        $scope.updateSearch();
      }
    };

    /**
     * Changes if all slides are shown or only slides belonging to current user
     *
     * @param {string} user
     *   This should either be 'mine' or 'all'.
     * @param {string} type
     *   The type to store the selection under.
     */
    $scope.setUser = function setUser(user, type) {
      // Save selection in localStorage.
      localStorage.setItem('overview.' + type + '.search_filter_default', user);

      if ($scope.showFromUser !== user) {
        $scope.showFromUser = user;

        $scope.setSearchFilters();
      }
    };
  }
]);
