/**
 * @file
 * Contains the slide overview directive.
 */

/**
 * Directive to show the slide overview.
 */
angular.module('ikApp').directive('ikSlideOverview', ['busService', '$filter',
  function (busService, $filter) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikSelectedSlides: '=',
        ikOverlay: '@'
      },
      controller: function ($scope, $filter, $controller, slideFactory, userService) {
        $controller('BaseSearchController', {$scope: $scope});

        // Get filter selection "all/mine" from localStorage.
        $scope.showFromUser = localStorage.getItem('overview.slide.search_filter_default') ?
          localStorage.getItem('overview.slide.search_filter_default') :
          'all';

        $scope.loading = false;

        // Set default orientation and sort.
        $scope.sort = {"created_at": "desc"};

        // Default pager values.
        $scope.pager = {
          "size": 6,
          "page": 0
        };
        $scope.hits = 0;

        // Slides to display.
        $scope.slides = [];

        // Setup default search options.
        var search = {
          "fields": ['title'],
          "text": $scope.search_text,
          "sort": {
            "created_at": {
              "order": "desc"
            }
          },
          'pager': $scope.pager
        };

        /**
         * Updates the slides array by send a search request.
         */
        $scope.updateSearch = function updateSearch() {
          // Get search text from scope.
          search.text = $scope.search_text;

          $scope.loading = true;

          slideFactory.searchSlides(search).then(
            function (data) {
              // Total hits.
              $scope.hits = data.hits;

              // Extract search ids.
              var ids = [];
              for (var i = 0; i < data.results.length; i++) {
                ids.push(data.results[i].id);
              }

              // Load slides bulk.
              slideFactory.loadSlidesBulk(ids).then(
                function success(data) {
                  $scope.slides = data;

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
         * Update search result on slide deletion.
         */
        $scope.$on('slide-deleted', function (data) {
          $scope.updateSearch();
        });

        /**
         * Changes if all slides are shown or only slides belonging to current user
         *
         * @param user
         *   This should either be 'mine' or 'all'.
         */
        $scope.setUser = function setUser(user) {
          // Save selection in localStorage.
          localStorage.setItem('overview.slide.search_filter_default', user);

          if ($scope.showFromUser !== user) {
            $scope.showFromUser = user;

            $scope.setSearchFilters();
          }
        };

        /**
         * Updates the search filter based on current orientation and user
         */
        $scope.setSearchFilters = function setSearchFilters() {
          delete search.filter;

          // No groups selected and "all" selected => select all groups and my.
          var selectedGroupIds = $filter('filter')($scope.userGroups, { selected: true }, true).map(function (group) {
            return group.id;
          });

          search.filter = $scope.baseBuildSearchFilter(selectedGroupIds);

          $scope.updateSearch();
        };

        /**
         * Returns true if slide is in selected slides array.
         *
         * @param slide
         * @returns {boolean}
         */
        $scope.slideSelected = function slideSelected(slide) {
          if (!$scope.ikSelectedSlides) {
            return false;
          }

          var res = false;

          $scope.ikSelectedSlides.forEach(function (element, index) {
            if (element.id == slide.id) {
              res = true;
            }
          });

          return res;
        };

        /**
         * Calculates if a scheduling is set and whether we are currently showing it or not.
         *
         * @param slide
         *   The current slide.
         *
         * @return
         *   True if the slide has a schedule set, and we are outside the scope of the schedule.
         */
        $scope.outOfSchedule = function outOfSchedule(slide) {
          if (slide.schedule_from && slide.schedule_to) { // From and to time is set.
            if (slide.schedule_from * 1000 < Date.now() && slide.schedule_to * 1000 > Date.now()) {
              // Current time is between from and to time (ie inside schedule).
              return false;
            }
            // Current time is set but is outside from and to time (ie out of schedule).
            return true;
          }
          // No schedule is set.
          return false;
        };

        /**
         * Changes the sort order and updated the slides.
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
         * Emits the slideOverview.clickSlide event.
         *
         * @param slide
         */
        $scope.slideOverviewClickSlide = function slideOverviewClickSlide(slide) {
          $scope.$emit('slideOverview.clickSlide', slide);
        };

        /**
         * Is the slide scheduled for now?
         *
         * @param slide
         */
        $scope.slideScheduledNow = function slideScheduledNow(slide) {
          if (!slide.published) {
            return false;
          }

          var now = new Date();
          now = parseInt(now.getTime() / 1000);

          if (slide.hasOwnProperty('schedule_from') && now < slide.schedule_from) {
            return false;
          }
          else if (slide.hasOwnProperty('schedule_to') && now > slide.schedule_to) {
            return false;
          }

          return true;
        };

        /**
         * Get scheduled text for slide.
         *
         * @param slide
         */
        $scope.getScheduledText = function getScheduledText(slide) {
          var text = '';

          if (!slide.published) {
            text = text + "Ikke udgivet!<br/>";
          }

          if (slide.hasOwnProperty('schedule_from')) {
            text = text + "Udgivet fra: " + $filter('date')(slide.schedule_from * 1000, "dd/MM/yyyy HH:mm") + ".<br/>";
          }

          if (slide.hasOwnProperty('schedule_to')) {
            text = text + "Udgivet til: " + $filter('date')(slide.schedule_to * 1000, "dd/MM/yyyy HH:mm") + ".";
          }

          return text;
        };
      },
      templateUrl: '/apps/ikApp/shared/elements/slideOverview/slide-overview-directive.html?' + window.config.version
    };
  }
]);
