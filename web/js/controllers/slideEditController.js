/**
 * @file
 * Contains slide edit controller.
 */

/**
 * Slide edit controller. Controls the editors for the slide creation process.
 */
ikApp.controller('SlideEditController', ['$scope', '$http', '$filter', 'mediaFactory', 'slideFactory',
  function($scope, $http, $filter, mediaFactory, slideFactory) {
    $scope.step = 'background-picker';
    $scope.addevent = {
      "title": null,
      "place" : null,
      "from" : null,
      "to" : null
    };

    // Get the slide from the backend.
    slideFactory.getEditSlide(null).then(function(data) {
      $scope.slide = data;
    });

    // Setup editor states and functions.
    $scope.editor = {
      editorOpen: false,
      toggleTextEditor: function() {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'partials/slide/editors/text-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleBackgroundEditor: function() {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'partials/slide/editors/background-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleLogoEditor: function() {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'partials/slide/editors/logo-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleManualCalendarEditor: function() {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'partials/slide/editors/manual-calendar-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }

        // Run sorting of events.
        $scope.sortEvents();
        $scope.validateEvents();
      },
      hideAllEditors: function() {
        $('html').removeClass('is-locked');

        $scope.editor.editorOpen = false;
        $scope.step = 'background-picker';
        $scope.logoStep = 'logo-picker';
        $scope.editorURL = '';
      }
    };


    /**
     * When clicking the background color button,
     * remove selected images.
     */
    $scope.clickBackgroundColor = function clickBackgroundColor() {
      $scope.slide.options.images = [];
    };

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
     * Sort events for slide.
     */
    $scope.sortEvents = function sortEvents() {
      if($scope.slide.options.eventitems.length > 0) {
        // Sort the events by from date.
        $scope.slide.options.eventitems = $filter('orderBy')($scope.slide.options.eventitems, "from")
      }
    };

    /**
     * Set outdated for events on slide
     */
    $scope.setOutdated = function setOutdated(event) {
      // Set current time.
      if (event.to * 1000 < Date.now()) {
        return true;
      }
      return false;
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

          // Save new event item with duration.
          $scope.slide.options.eventitems[i] = item;
        }
      }
    };

    // Register event listener for select media.
    $scope.$on('mediaOverview.selectMedia', function(event, media) {
      if (media.media_type === 'logo') {
        $scope.slide.logo = media;

        $scope.logoStep = 'logo-picker';
      }
      else {
        var containsMedia = false;

        $scope.slide.media.forEach(function(element) {
          if (element.id === media.id) {
            containsMedia = true;
          }
        });

        if (containsMedia) {
          $scope.slide.media.length = 0;
        }
        else {
          $scope.slide.media.length = 0;
          $scope.slide.media.push(media);
        }

        // Hide editors.
        $scope.editor.hideAllEditors();
      }
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
        mediaFactory.getMedia(data.id).then(function(media) {
          if (media.media_type === 'logo') {
            $scope.slide.logo = media;

            $scope.logoStep = 'logo-picker';
          }
          else {
            $scope.slide.media.length = 0;
            $scope.slide.media.push(media);

            // Hide editors.
            $scope.editor.hideAllEditors();
          }
        });
      }
    });


    $scope.step = 'background-picker';

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
      $scope.step = 'pick-from-media';
      $scope.$emit('mediaOverview.updateSearch');
    };

    /**
     * Set the step to pick-from-computer.
     */
    $scope.pickFromComputer = function pickFromComputer() {
      $scope.step = 'pick-from-computer';
    };

    $scope.logoStep = 'logo-picker';

    /**
     * Set the step to logo-picker.
     */
    $scope.logoPicker = function logoPicker() {
      $scope.logoStep = 'logo-picker';
    };

    /**
     * Set the step to pick-logo-from-media.
     */
    $scope.pickLogoFromMedia = function pickLogoFromMedia() {
      $scope.logoStep = 'pick-logo-from-media';
      $scope.$emit('mediaOverview.updateSearch');
    };

    /**
     * Set the step to pick-logo-from-computer.
     */
    $scope.pickLogoFromComputer = function pickLogoFromComputer() {
      $scope.logoStep = 'pick-logo-from-computer';
    };

    $scope.logoPositions = [
      {
        value: "top: 0; left: 0; max-width: 10%; max-height: 10%",
        text: "top venstre 10%"
      },
      {
        value: "top: 0; right: 0; max-width: 10%; max-height: 10%",
        text: "top højre 10%"
      },
      {
        value: "bottom: 0; left: 0; max-width: 10%; max-height: 10%",
        text: "bund venstre 10%"
      },
      {
        value: "bottom: 0; right: 0; max-width: 10%; max-height: 10%",
        text: "bund højre 10%"
      },
      {
        value: "top: 0; left: 0; max-width: 20%; max-height: 20%",
        text: "top venstre 20%"
      },
      {
        value: "top: 0; right: 0; max-width: 20%; max-height: 20%",
        text: "top højre 20%"
      },
      {
        value: "bottom: 0; left: 0; max-width: 20%; max-height: 20%",
        text: "bund venstre 20%"
      },
      {
        value: "bottom: 0; right: 0; max-width: 20%; max-height: 20%",
        text: "bund højre 20%"
      }

    ];
  }
]);
