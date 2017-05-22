/**
 * @file
 * Contains the media overview directive.
 */

/**
 * Media Overview Directive.
 *
 * Directive to insert a media overview.
 *
 * Emits the mediaOverview.selectMedia event when a media from the overview has been clicked.
 *
 * @param media-type
 *   which media type should be shown, "image" or "video",
 *   leave out show all media.
 */
angular.module('ikApp').directive('ikMediaOverview', [
  'busService',
  function (busService) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikMediaType: '@',
        ikAutoSearch: '@',
        ikHideFilters: '=',
        ikSelectedMedia: '='
      },
      controller: function ($scope, $filter, mediaFactory, userService) {
        // Set default orientation and sort.
        $scope.sort = {"created_at": "desc"};
        $scope.loading = false;

        // Get current user groups.
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

        // Set default search text.
        $scope.search_text = '';

        // Set default media type.
        $scope.media_type = 'all';

        // Media to display.
        $scope.media = [];

        // Default pager values.
        $scope.pager = {
          "size": 6,
          "page": 0
        };
        $scope.hits = 0;

        // Setup default search options.
        var search = {
          "fields": 'name',
          "text": '',
          "sort": {
            "created_at": {
              "order": "desc"
            }
          },
          'pager': $scope.pager,
          "filter": {}
        };

        /**
         * Updates the images array by sending a search request.
         */
        $scope.updateSearch = function updateSearch() {
          // Get search text from scope.
          search.text = $scope.search_text;

          $scope.loading = true;

          mediaFactory.searchMedia(search).then(
            function (data) {
              // Total hits.
              $scope.hits = data.hits;

              // Extract search ids.
              var ids = [];
              for (var i = 0; i < data.results.length; i++) {
                ids.push(data.results[i].id);
              }

              mediaFactory.loadMediaBulk(ids).then(
                function success(data) {
                  $scope.media = data;

                  $scope.loading = false;
                },
                function error(reason) {
                  busService.$emit('log.error', {
                    'cause': reason,
                    'msg': 'Hentning af søgeresultater fejlede.'
                  });
                  $scope.loading = false;
                }
              );
            }
          );
        };

        /**
         * Returns true if media is in selected media array.
         *
         * @param media
         * @returns {boolean}
         */
        $scope.mediaSelected = function mediaSelected(media) {
          if (!$scope.ikSelectedMedia) {
            return false;
          }

          var res = false;

          $scope.ikSelectedMedia.forEach(function (element) {
            if (element.id === media.id) {
              res = true;
            }
          });

          return res;
        };

        /**
         * Set the media type to filter on.
         * @param type
         */
        $scope.filterMediaType = function filterMediaType(type) {
          // Only update search if value changes.
          if ($scope.media_type !== type) {
            // Update scope to show selection in GUI.
            $scope.media_type = type;

            $scope.setSearchFilters();
          }
        };

        /**
         * Updates the search filter based on current orientation and user
         */
        $scope.setSearchFilters = function setSearchFilters() {
          delete search.filter;

          var filter = {
            "query": {
              "bool": {
                "must": [],
                "should": []
              }
            }
          };

          // Filter based on media type.
          if ($scope.media_type !== 'all') {
            var term = {};
            term.term = {
              media_type: $scope.media_type
            };
            filter.query.bool.must.push(term);
          }

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

          // No groups selected and "all" selected => select all groups and my.
          var selectedGroupIds = $filter('filter')($scope.userGroups, { selected: true }, true).map(function (group) {
            return group.id;
          });

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

          search.filter = filter;

          $scope.updateSearch();
        };

        /**
         * Changes if all slides are shown or only slides belonging to current user
         *
         * @param user
         *   This should either be 'mine' or 'all'.
         */
        $scope.setUser = function setUser(user) {
          // Save selection in localStorage.
          localStorage.setItem('overview.media.search_filter_default', user);

          if ($scope.showFromUser !== user) {
            $scope.showFromUser = user;

            $scope.setSearchFilters();
          }
        };

        /**
         * Changes the sort order and updated the images.
         *
         * @param sort_field
         *   Field to sort on.
         * @param sort_order
         *   The order to sort in 'desc' or 'asc'.
         */
        $scope.setSort = function setSort(sort_field, sort_order) {
          // Only update search if sort have changed.
          if ($scope.sort[sort_field] === undefined || $scope.sort[sort_field] !== sort_order) {
            // Update the store sort order.
            $scope.sort = {};
            $scope.sort[sort_field] = sort_order;

            // Update the search variable.
            search.sort = {};
            search.sort[sort_field] = {
              "order": sort_order
            };

            $scope.updateSearch();
          }
        };

        /**
         * Emits event when the user clicks a media.
         *
         * @param mediaElement
         */
        $scope.mediaOverviewClickMedia = function mediaOverviewClickImage(mediaElement) {
          $scope.$emit('mediaOverview.selectMedia', mediaElement);
        };

        /**
         * Handle mediaOverview.updateSearch events.
         */
        $scope.$on('mediaOverview.updateSearch', function (event) {
          $scope.updateSearch();

          event.preventDefault();
        });

        // Get current user.
        $scope.currentUser = userService.getCurrentUser();

        // Get filter selection "all/mine" from localStorage.
        $scope.showFromUser = localStorage.getItem('overview.media.search_filter_default') ?
          localStorage.getItem('overview.media.search_filter_default') :
          'all';


        $scope.$on('$destroy', function () {
          cleanupGetCurrentUserGroups();
        })
      },
      link: function (scope, element, attrs) {
        attrs.$observe('ikMediaType', function (val) {
          if (!val) {
            return;
          }
          if (val === scope.media_type) {
            return;
          }

          scope.filterMediaType(val);
        });
      },
      templateUrl: '/apps/ikApp/shared/elements/mediaOverview/media-overview-directive.html?' + window.config.version
    };
  }
]);
