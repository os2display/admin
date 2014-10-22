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
ikApp.directive('ikMediaOverview', function() {
  return {
    restrict: 'E',
    scope: {
      ikMediaType: '@',
      ikAutoSearch: '@',
      ikHideFilters: '=',
      ikSelectedMedia: '='
    },
    controller: function($scope, mediaFactory) {
      // Set default orientation and sort.
      $scope.sort = { "created_at": "desc" };

      // Set default search text.
      $scope.search_text = '';

      // Set default media type.
      $scope.media_type = '';

      // Media to display.
      $scope.media = [];

      // Default pager values.
      $scope.pager = {
        "size": 9,
        "page": 0
      };
      $scope.hits = 0;

      // Setup default search options.
      var search = {
        "fields": 'name',
        "text": '',
        "sort": {
          "created_at" : {
            "order": "desc"
          }
        },
        'pager': $scope.pager
      };

      // Mouse hover on image.
      $scope.hovering = false;

      /**
       * Adds hover overlay on media elements.
       */
      $scope.mouseHover = function mouseHover(state) {
        if(state > 0) {
          $scope.hovering = state;
        } else {
          $scope.hovering = false;
        }
      };

      /**
       * Updates the images array by sending a search request.
       */
      $scope.updateSearch = function updateSearch() {
        // Get search text from scope.
        search.text = $scope.search_text;

        mediaFactory.searchMedia(search).then(
          function(data) {
            // Total hits.
            $scope.hits = data.hits;

            // Extract search ids.
            var ids = [];
            for (var i = 0; i < data.results.length; i++) {
              ids.push(data.results[i].id);
            }

            mediaFactory.loadMediaBulk(ids).then(
              function(data) {
                $scope.media = data;
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

        $scope.ikSelectedMedia.forEach(function(element, index) {
          if (element.id == media.id) {
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

          // Remove filter if no type is set.
          if (type === '') {
            search.filter = undefined;
          }
          else {
            // Filter based on content type.
            search.filter = {
              "bool": {
                "must": {
                  "term": {
                    "content_type":  type
                  }
                }
              }
            };
          }

          // Update the search result.
          $scope.updateSearch();
        }
      };

      /**
       * Get the content type of a media: image or media
       *
       * @param mediaElement
       *
       * @returns "", "video" or "image"
       */
      $scope.getMediaType = function getMediaType(mediaElement) {
        if (!mediaElement) {
          return "";
        }

        var type = mediaElement.content_type.split("/");
        return type[0];
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
      $scope.$on('mediaOverview.updateSearch', function(event) {
        $scope.updateSearch();

        event.preventDefault();
      });

      // Send the default search query.
      if ($scope.ikAutoSearch) {
        $scope.updateSearch();
      }
    },
    link: function(scope, element, attrs) {
      attrs.$observe('mediaType', function(val) {
        if (!val) {
          return;
        }
        if (val == scope.media_type) {
          return;
        }

        scope.filterMediaType(val);
      })
    },
    templateUrl: 'partials/directives/media-overview-directive.html'
  };
});
