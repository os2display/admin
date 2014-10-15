/**
 * @file
 * Contains slide directives to display and edit a slide.
 */

/**
 * Directive to insert html for a slide.
 * @param ik-id: the id of the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlide', ['slideFactory', 'templateFactory', function(slideFactory, templateFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikSlide: '='
    },
    link: function(scope, element, attrs) {
      scope.templateURL = '/partials/slide/slide-loading.html';

      // Observe for changes to the ik-slide attribute. Setup slide when ik-slide is set.
      attrs.$observe('ikSlide', function(val) {
        if (!val) {
          return;
        }

        scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

        if (scope.ikSlide.media_type === 'image') {
          if (scope.ikSlide.media.length > 0) {
            scope.ikSlide.currentImage = scope.ikSlide.media[0].urls.default_landscape_small;
          }
          else {
            scope.ikSlide.currentImage = '';
          }
        }
        else {
          if (scope.ikSlide.media.length > 0) {
            if (scope.ikSlide.media[0] === undefined) {
              scope.ikSlide.currentImage = "";
            }
            else {
              scope.ikSlide.currentImage = scope.ikSlide.media[0].provider_metadata[0].thumbnails[1].reference;
            }
          }
          else {
            scope.ikSlide.currentVideo = {"mp4": "", "ogg": ""};
          }
        }

        // Get the template.
        scope.template = templateFactory.getTemplate(scope.ikSlide.template);

        // Setup inline styling.
        scope.theStyle = {
          width: "" + scope.ikWidth + "px",
          height: "" + parseFloat(scope.template.idealdimensions.height * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px",
          fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px"
        }
      });
    },
    template: '<div class="preview--slide" data-ng-include="" src="templateURL"></div>'
  }
}]);

/**
 * Directive to insert html for a slide, that is editable.
 * @param ik-slide: the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlideEditable', ['templateFactory', function($templateFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikSlide: '='
    },
    link: function(scope, element, attrs) {
      scope.templateURL = '/partials/slide/slide-loading.html';

      // Watch for changes to ikSlide.
      scope.$watch('ikSlide', function (newVal, oldVal) {
        if (!newVal) return;

        if (scope.ikSlide.media_type === 'image') {
          if (scope.ikSlide.media.length > 0) {
            scope.ikSlide.currentImage = scope.ikSlide.media[0].urls.default_landscape;
          }
          else {
            scope.ikSlide.currentImage = '';
          }
        }
        else {
          if (scope.ikSlide.media.length > 0) {
            if (scope.ikSlide.media[0] === undefined) {
              scope.ikSlide.currentVideo = {"mp4": "", "ogg": ""};
            }
            else {
              // Set current video variable to path to video files.
              scope.ikSlide.currentVideo = {
                "mp4": scope.ikSlide.media[0].provider_metadata[0].reference,
                "ogg": scope.ikSlide.media[0].provider_metadata[1].reference
              };

              // Reload video player.
              setTimeout(function () {
                element.find('#videoPlayer').load();
              }, 1000);
            }
          }
          else {
            scope.ikSlide.currentVideo = {"mp4": "", "ogg": ""};
          }
        }

        if (!scope.template || newVal.template !== oldVal.template) {
          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '-edit.html';
          scope.template = $templateFactory.getTemplate(scope.ikSlide.template);
        }

        if (!scope.theStyle) {
          // Setup the inline styling
          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(scope.template.idealdimensions.height * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px",
            fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px"
          }
        } else {
          // Update fontsize
          scope.theStyle.fontsize =  "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px";
        }
      }, true);
    },
    templateUrl: '/partials/slide/slide-edit.html'
  }
}]);

/**
 * Directive to show the slide overview.
 */
ikApp.directive('ikSlideOverview', function() {
  return {
    restrict: 'E',
    scope: {
      ikSelectedSlides: '='
    },
    controller: function($scope, slideFactory) {
      // Set default orientation and sort.
      $scope.orientation = 'all';
      $scope.sort = {"created_at": "desc"};

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
        }
      };

      /**
       * Updates the slides array by send a search request.
       */
      $scope.updateSearch = function updateSearch() {
        // Get search text from scope.
        search.text = $scope.search_text;

        slideFactory.searchSlides(search).then(
          function (data) {
            // Extract search ids.
            var ids = [];
            for (var i = 0; i < data.length; i++) {
              ids.push(data[i].id);
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
       * Changes orientation and updated the slides.
       *
       * @param orientation
       *   This should either be 'landscape' or 'portrait'.
       */
      $scope.setOrientation = function setOrientation(orientation) {
        if ($scope.orientation !== orientation) {
          $scope.orientation = orientation;

          // Update search query.

          // Update orientation for the search.
          delete search.filter;
          if (orientation !== 'all') {
            search.filter = {
              "bool": {
                "must": {
                  "term": {
                    "orientation": orientation
                  }
                }
              }
            };
          }

          $scope.updateSearch();
        }
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

      // Send the default search query.
      $scope.updateSearch();
    },
    templateUrl: '/partials/directives/slide-overview.html'
  }
});
