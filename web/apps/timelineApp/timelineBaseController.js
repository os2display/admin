/**
 * @file
 * Base controller for time-line controllers.
 */

/**
 * Time line base controller.
 */
angular.module('timelineApp').controller('TimelineBaseController', ['busService', 'timelineService', '$scope',
  function (busService, timelineService, $scope) {
    'use strict';

    $scope.loading = false;

    // Set default values.
    $scope.sort = { "created_at": "desc" };

    // Default pager values.
    $scope.pager = {
      "size": 6,
      "page": 0
    };
    $scope.hits = 0;

    // Setup default search options.
    var search = {
      "type": $scope.searchType,
      "fields": [ 'title' ],
      "text": '',
      "sort": {
        "created_at" : {
          "order": "desc"
        }
      },
      'pager': $scope.pager,
      'callbacks': {
        'hits': '',
        'error': ''
      }
    };

    /**
     * Updates the screens array by send a search request.
     */
    $scope.updateSearch = function() {
      // Get search text from scope.
      search.text = $scope.search_text;

      $scope.loading = true;

      var uuid = CryptoJS.MD5(JSON.stringify(search)).toString();
      search.callbacks.hits = 'searchService.hits-' + uuid;
      search.callbacks.error = 'searchService.error-' + uuid;

      busService.$once(search.callbacks.hits, function(event, data) {
          // Total hits.
          $scope.hits = data.hits;

          // Extract search ids.
          var ids = [];
          for (var i = 0; i < data.results.length; i++) {
            ids.push(data.results[i].id);
          }

          // Load slides bulk.
          timelineService.fetchData(ids).then(
            function success(data) {
              $scope.data = data;
              $scope.loading = false;
            },
            function error(reason) {
              busService.$emit('log.error', {
                'cause': reason,
                'msg': 'Kunne ikke load skærme fra databasen.'
              });
              $scope.loading = false;
            }
          );
        }
      );

      busService.$once(search.callbacks.error, function(event, args) {
        busService.$emit('log.error', {
          'cause': args,
          'msg': 'Kunne ikke hente søgeresultater.'
        });
      });

      busService.$emit('searchService.request', search);
    };

    /**
     * Update search result with pager reset.
     */
    $scope.search = function search() {
      // Reset pager.
      $scope.pager.page = 0;

      $scope.updateSearch();
    };

    /**
     * Changes if all slides are shown or only slides belonging to current user
     *
     * @param user
     *   This should either be 'mine' or 'all'.
     */
    $scope.setUser = function setUser(user) {
      if ($scope.showFromUser !== user) {
        $scope.showFromUser = user;

        $scope.setSearchFilters();
      }
    };

    /**
     * Updates the search filter based on current orientation and user
     */
    $scope.setSearchFilters = function setSearchFilters() {
      // Update orientation for the search.
      delete search.filter;

      if($scope.showFromUser !== 'all') {
        search.filter = {
          "bool": {
            "must": []
          }
        }
      }

      if ($scope.showFromUser !== 'all') {
        var term = {};
        term.term = {user : $scope.currentUser.id};
        search.filter.bool.must.push(term);
      }

      $scope.search();
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
        search.sort = { };
        search.sort[sort_field] = {
          "order": sort_order
        };

        $scope.updateSearch();
      }
    };

    /**
     * Click handler to change page.
     *
     * @param page
     */
    $scope.changePage = function changePage(page) {
      $scope.pager.page = page;
      $scope.updateSearch();
    };

    /**
     * Load current user.
     */
    busService.$on('userService.returnUser', function returnUser(event, user) {
        $scope.currentUser = user;

        // Set search filter default
        $scope.showFromUser = $scope.currentUser.search_filter_default;

        // Updated search filters (build "mine" filter with user id). It
        // will trigger an search update.
        $scope.setSearchFilters();
      }
    );
    busService.$emit('userService.requestUser');
  }
]);