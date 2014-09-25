/**
 * @file
 * Contains slide edit controller.
 */

/**
 * Slide edit controller. Controls the slide creation process.
 */
ikApp.controller('SlideEditController', ['$scope', '$http', '$filter', 'mediaFactory', 'slideFactory',
  function($scope, $http, $filter, mediaFactory, slideFactory) {
    $scope.step = 'background-picker';

    // Get the slide from the backend.
    slideFactory.getEditSlide(null).then(function(data) {
      $scope.slide = data;

      // @TODO: refactor to check on media_type on slide instead of template.
      if ($scope.slide.template === 'only-video') {
        $scope.selectedMediaType = 'video';
      } else {
        $scope.selectedMediaType = 'image';
      }
    });

    // Setup editor states and functions.
    $scope.editor = {
      showTextEditor: false,
      showBackgroundEditor: false,
      showVideoEditor: false,
      toggleTextEditor: function() {
        $scope.editor.showBackgroundEditor = false;
        $scope.editor.showVideoEditor = false;
        $scope.editor.showContentEditor = false;
        $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
      },

      toggleBackgroundEditor: function() {
        $scope.step = 'background-picker';
        $scope.editor.showTextEditor = false;
        $scope.editor.showVideoEditor = false;
        $scope.editor.showContentEditor = false;
        $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
      },

      toggleVideoEditor: function() {
        $scope.editor.showTextEditor = false;
        $scope.editor.showBackgroundEditor = false;
        $scope.editor.showContentEditor = false;
        $scope.editor.showVideoEditor = !$scope.editor.showVideoEditor;
      },

      toggleContentEditor: function() {
        // Hide all other editors.
        $scope.editor.showTextEditor = false;
        $scope.editor.showVideoEditor = false;
        $scope.editor.showBackgroundEditor = false;

        //Show content editor.
        $scope.editor.showContentEditor = !$scope.editor.showContentEditor;

        // Run sorting of events.
        $scope.sortEvents();
        $scope.validateEvents();
      }
    }

    /**
     * Set the step to background-picker.
     */
    $scope.backgroundPicker = function backgroundPicker() {
      $scope.step = 'background-picker';
    };

    /**
     * Set the step to pick-from-media.
     */
    $scope.pickFromMedia = function pickFromMedia() {
      $scope.$broadcast('mediaOverview.updateSearch');
      $scope.step = 'pick-from-media';
    };

    /**
     * Set the step to pick-from-computer.
     */
    $scope.pickFromComputer = function pickFromComputer() {
      $scope.step = 'pick-from-computer';
    };

    /**
     * When clicking the background color button,
     * remove selected images.
     */
    $scope.clickBackgroundColor = function clickBackgroundColor() {
      $scope.slide.options.images = [];
    }

    // Register event listener for select media.
    $scope.$on('mediaOverview.selectMedia', function(event, media) {
      // Handle selection of video or image.
      if (media.content_type.indexOf('image/') === 0) {
        var index = $scope.slide.options.images.indexOf(media.id);

        if (index > -1) {
          $scope.slide.options.images.splice(index, 1);
        }
        else {
          $scope.slide.options.images = [];
          $scope.slide.options.images.push(media.id);
          $scope.slide.imageUrls = [];
          $scope.slide.imageUrls[media.id] = media.urls;
        }
      }
      else if (media.content_type.indexOf('video/') === 0) {
        var index = $scope.slide.options.videos.indexOf(media.id);

        if (index > -1) {
          $scope.slide.options.videos.splice(index, 1);
        }
        else {
          $scope.slide.options.videos = [];
          $scope.slide.options.videos.push(media.id);
          $scope.slide.videoUrls = [];
          $scope.slide.videoUrls[media.id] = {
            "mp4": media.provider_metadata[0].reference,
            "ogg": media.provider_metadata[1].reference
          };
        }
      }

      // Reset step to background-picker.
      $scope.step = 'background-picker';

      // Hide editors.
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showTextEditor = false;
    });

    // Register event listener for media upload success.
    $scope.$on('mediaUpload.uploadSuccess', function(event, data) {
      var allSuccess = true;

      for (var i = 0; i < data.queue.length; i++) {
        var item = data.queue[i];

        if (!item.isSuccess) {
          allSuccess = false;
          break;
        }
      }

      // If all the data items were uploaded correctly.
      if (allSuccess) {
        mediaFactory.getMedia(data.id).then(function(image) {
          $scope.slide.options.images = [];
          $scope.slide.options.images.push(image.id);
          $scope.slide.imageUrls = [];
          $scope.slide.imageUrls[image.id] = image.urls;
        });

        // Reset step to background-picker.
        $scope.step = 'background-picker';

        // Hide editors.
        $scope.editor.showBackgroundEditor = false;
        $scope.editor.showTextEditor = false;
      }
    });


    /**
     * Add event to slide
     */
    $scope.addEventItem = function addEventItem() {
      var event = {
        "title": $scope.addevent.title,
        "place" : $scope.addevent.place,
        "from" : $scope.addevent.from,
        "to" : $scope.addevent.to
      };

      // Add event data to slide array.
      $scope.slide.options.eventitems.push(event);

      // Reset input fields.
      $scope.addevent.title = null;
      $scope.addevent.place = null;
      $scope.addevent.from = null;
      $scope.addevent.to = null;
    };


    /**
     * Remove event from slide.
     */
    $scope.removeEventItem = function removeEventItem(event) {
      $scope.slide.options.eventitems.splice($scope.slide.options.eventitems.indexOf(event), 1);
    };


    /**
     * Remove event from slide.
     */
    $scope.sortEvents = function sortEvents() {
      if($scope.slide.options.eventitems.length > 0) {
        // Sort the events by from date.
        $scope.slide.options.eventitems = $filter('orderBy')($scope.slide.options.eventitems, "from")
      }
    };


    /**
     * Validate events related to the slide.
     */
    $scope.validateEvents = function validateEvents() {
      if($scope.slide.options.eventitems.length > 0) {
        // Run through all events.
        for (var i = 0; i < $scope.slide.options.eventitems.length; i++) {
          var item = $scope.slide.options.eventitems[i];

          // Set daily event default.
          item.dailyEvent = false;

          // Set duration for event item.
          item.duration = item.to - item.from;

          // Check if the duration is less than 24 hours.
          if (item.duration < 86400) {
            item.dailyEvent = true;
          }

          // Save new event item with duration
          $scope.slide.options.eventitems[i] = item;
        }
      }
    };
  }
]);


/**
 * Add a reverse filter to eventlist.
 */
ikApp.filter('reverseEvents', function() {
  return function(items) {
    if (!angular.isArray(items)){
      return false
    }
    return items.slice().reverse();
  };
});
