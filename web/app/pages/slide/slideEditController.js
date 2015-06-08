/**
 * @file
 * Contains slide edit controller.
 */

/**
 * Slide edit controller. Controls the editors for the slide creation process.
 */
angular.module('ikApp').controller('SlideEditController', ['$scope', '$http', '$filter', 'mediaFactory', 'slideFactory', 'kobaFactory', 'itkLogFactory',
  function ($scope, $http, $filter, mediaFactory, slideFactory, kobaFactory, itkLogFactory) {
    'use strict';

    $scope.step = 'background-picker';
    $scope.addevent = {
      "title": null,
      "place": null,
      "from": null,
      "to": null
    };

    // Get the slide from the backend.
    slideFactory.getEditSlide(null).then(
      function success(data) {
        $scope.slide = data;
      },
      function error(reason) {
        itkLogFactory.error("Kunne ikke hente slide.", reason);
      }
    );

    // Setup editor states and functions.
    $scope.editor = {
      editorOpen: false,
      toggleTextEditor: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/text-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleHeaderEditorResponsive: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/header-editor-responsive.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleBackgroundEditorTransparent: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/background-editor-transparent.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleBackgroundEditor: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/background-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleLogoEditor: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/logo-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleManualCalendarEditor: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          // Reset input fields.
          $scope.addevent.title = null;
          $scope.addevent.place = null;
          $scope.addevent.from = null;
          $scope.addevent.to = null;

          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/manual-calendar-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }

        // Run sorting of events.
        $scope.sortEvents();
        $scope.validateEvents();
      },
      toggleEventCalendarEditor: function () {
        $('html').toggleClass('is-locked');

        kobaFactory.getResources().then(
          function (data) {
            $scope.availableResources = data;
          }
        );

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/event-calendar-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleSourceEditor: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/source-editor.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      toggleStaticBackgroundColorEditor: function () {
        $('html').toggleClass('is-locked');

        if (!$scope.editor.editorOpen) {
          $scope.editor.editorOpen = true;
          $scope.editorURL = 'app/shared/elements/slide/editors/background-editor-static-colors.html';
        } else {
          $scope.editor.editorOpen = false;
          $scope.editorURL = '';
        }
      },
      hideAllEditors: function () {
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
        "place": $scope.addevent.place,
        "from": $scope.addevent.from,
        "to": $scope.addevent.to
      };

      // Add event data to slide array.
      $scope.slide.options.eventitems.push(event);

      // Reset input fields.
      $scope.addevent.title = null;
      $scope.addevent.place = null;
      $scope.addevent.from = null;
      $scope.addevent.to = null;

      event = null;
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
      if ($scope.slide.options.eventitems.length > 0) {
        // Sort the events by from date.
        $scope.slide.options.eventitems = $filter('orderBy')($scope.slide.options.eventitems, "from")
      }
    };

    /**
     * Is outdated for events on slide
     */
    $scope.eventIsOutdated = function setOutdated(event) {
      var to = event.to;
      var from = event.from;
      var now = Date.now() / 1000;

      return (to && now > to) || (!to && now > from);
    };

    /**
     * Validate events related to the slide.
     */
    $scope.validateEvents = function validateEvents() {
      if ($scope.slide.options.eventitems.length > 0) {
        // Run through all events.
        for (var i = 0; i < $scope.slide.options.eventitems.length; i++) {
          var item = $scope.slide.options.eventitems[i];

          if (item.from && !item.to) {
            item.dailyEvent = true;
          }
          else {
            var fromDate = new Date(item.from * 1000);
            var toDate = new Date(item.to * 1000);

            return fromDate.getDate() === toDate.getDate();
          }

          // Save new event item with duration.
          $scope.slide.options.eventitems[i] = item;
        }
      }
    };

    /**
     * Add calendar events from source (for event calendar.)
     */
    $scope.addCalendarEvents = function addCalendarEvents() {
      var arr = [];

      // Process bookings for each resource.
      var addResourceBookings = function (data) {
        for (var i = 0; i < data.length; i++) {
          var event = data[i];
          arr.push(event);
        }
      };

      // Get bookings for each resource.
      for (var i = 0; i < $scope.slide.options.resources.length; i++) {
        var resource = $scope.slide.options.resources[i];
        var now = new Date();
        var todayStart = (new Date(now.getFullYear(), now.getMonth(), now.getDate())).getTime() / 1000;
        var todayEnd = todayStart + 86400;

        kobaFactory.getBookingsForResource(resource.mail, todayStart, todayEnd).then(
          addResourceBookings,
          function error(reason) {
            itkLogFactory.error("Kunne ikke hente bookings for ressource", reason);
          }
        );
      }

      $scope.slide.calendar_events = arr;
    };

    // Register event listener for select media.
    $scope.$on('mediaOverview.selectMedia', function (event, media) {
      if (media.media_type === 'logo') {
        $scope.slide.logo = media;

        $scope.logoStep = 'logo-picker';
      }
      else {
        var containsMedia = false;

        $scope.slide.media.forEach(function (element) {
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
    $scope.$on('mediaUpload.uploadSuccess', function (event, data) {
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
        mediaFactory.getMedia(data.id).then(
          function success(media) {
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
          },
          function error(reason) {
            itkLogFactory.error("Kunne ikke tilføje media.", reason);
          }
        );
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

    /**
     * Available logo positions.
     */
    $scope.logoPositions = [
      {
        value: {
          'top': '0',
          'bottom': 'auto',
          'left': '0',
          'right': 'auto'
        },
        text: 'top venstre'
      },
      {
        value: {
          'top': '0',
          'bottom': 'auto',
          'left': 'auto',
          'right': '0'
        },
        text: 'top højre'
      },
      {
        value: {
          'top': 'auto',
          'bottom': '0',
          'left': '0',
          'right': 'auto'
        },
        text: 'bund venstre'
      },
      {
        value: {
          'top': 'auto',
          'bottom': '0',
          'left': 'auto',
          'right': '0'
        },
        text: 'bund højre'
      }
    ];

    /**
     * Available logo sizes.
     */
    $scope.logoSizes = [
      {
        value: "5%",
        text: "Meget lille (5% af skærmen)"
      },
      {
        value: "10%",
        text: "Lille (10% af skærmen)"
      },
      {
        value: "15%",
        text: "Medium (15% af skærmen)"
      },
      {
        value: "20%",
        text: "Stor (20% af skærmen)"
      },
      {
        value: "40%",
        text: "Ekstra stor (40% af skærmen)"
      }
    ];
  }
]);
