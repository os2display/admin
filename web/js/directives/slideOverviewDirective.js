/**
 * @file
 * Contains the slide overview directive.
 */

/**
 * Directive to show the slide overview.
 */
ikApp.directive('ikSlideOverview', function() {
  "use strict";

  return {
    restrict: 'E',
    scope: {
      ikSelectedSlides: '=',
      ikHideFilters: '=',
      ikFilter: '@',
      ikOverlay: '@'
    },
    controller: function($scope, slideFactory, userFactory) {
      // Set default orientation and sort.
      $scope.orientation = 'all';
      $scope.showFromUser = 'all';
      $scope.sort = {"created_at": "desc"};

      userFactory.getCurrentUser().then(
        function(data) {
          $scope.currentUser = data;
        }
      );

      // Default pager values.
      $scope.pager = {
        "size": 9,
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
              function (data) {
                $scope.slides = data;
              }
            );
          }
        );
      };

      /**
       * Update search result on slide deletion.
       */
      $scope.$on('slide-deleted', function(data) {
        $scope.updateSearch();
      });

      /**
       * Changes orientation and updated the slides.
       *
       * @param orientation
       *   This should either be 'landscape' or 'portrait'.
       */
      $scope.setOrientation = function setOrientation(orientation) {
        if ($scope.orientation !== orientation) {
          $scope.orientation = orientation;

          $scope.setSearchFilters();
          $scope.updateSearch();
        }
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
          $scope.updateSearch();
        }
      };

      /**
       * Updates the search filter based on current orientation and user
       */
      $scope.setSearchFilters = function setSearchFilters() {
        // Update orientation for the search.
        delete search.filter;

        if($scope.orientation !== 'all' || $scope.showFromUser !== 'all') {
          search.filter = {
            "bool": {
              "must": []
            }
          }
        }

        if ($scope.orientation !== 'all') {
          var term = new Object();
          term.term = {orientation : $scope.orientation};
          search.filter.bool.must.push(term);
        }

        if ($scope.showFromUser !== 'all') {
          var term = new Object();
          term.term = {user : $scope.currentUser.id};
          search.filter.bool.must.push(term);
        }

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

        $scope.ikSelectedSlides.forEach(function(element, index) {
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

      // Set filter if parameter ikFilter is set.
      if ($scope.ikFilter) {
        $scope.setOrientation($scope.ikFilter);
      } else {
        // Send the default search query.
        $scope.updateSearch();
      }
    },
    templateUrl: '/partials/directives/slide-overview-directive.html'
  };
});
