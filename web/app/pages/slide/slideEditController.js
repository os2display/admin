/**
 * @file
 * Contains slide edit controller.
 */

/**
 * Slide edit controller. Controls the editors for the slide creation process.
 */
angular.module('ikApp').controller('SlideEditController', ['$scope', '$http', '$filter', 'mediaFactory', 'slideFactory', 'kobaFactory', 'busService', 'templateFactory',
  function ($scope, $http, $filter, mediaFactory, slideFactory, kobaFactory, busService, templateFactory) {
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

        templateFactory.getSlideTemplate(data.template).then(
          function success(data) {
            $scope.template = data;
          },
          function error(reason) {
            busService.$emit('log.error', {
              'cause': reason,
              'msg': 'Kunne ikke loade værktøjer til slidet.'
            });
          }
        );
      },
      function error(reason) {
        busService.$emit('log.error', {
          'cause': reason,
          'msg': 'Kunne ikke hente slide.'
        });
      }
    );

    /**
     * Open the selected tool.
     * @param tool
     */
    $scope.openTool = function openTool(tool) {
      busService.$emit('bodyService.toggleClass', 'is-locked');
      if (!$scope.editor.editorOpen) {
        if (tool.id === 'manual-calendar-editor') {
          // Reset input fields.
          $scope.addevent.title = null;
          $scope.addevent.place = null;
          $scope.addevent.from = null;
          $scope.addevent.to = null;

          // Run sorting of events.
          $scope.sortEvents();
        }
        else if (tool.id === 'event-calendar-editor') {
          // Reset resources.
          $scope.availableResources = [];

          // Get resources for the calendar.
          kobaFactory.getResources().then(
            function (data) {
              // Store data in the scope.
              $scope.availableResources = data;
              // Filter the current slides options based on the resources
              // available.
              if ($scope.slide.options.hasOwnProperty('resources')) {
                var selected = [];
                var len = $scope.slide.options.resources.length;
                for (var i = 0; i < len; i++) {
                  var found = false;
                  for (var j = 0; j < data.length; j++) {
                    if (data[j].mail === $scope.slide.options.resources[i].mail) {
                      found = true;
                      break;
                    }
                  }

                  if (found) {
                    // Item is found, so add it to the list.
                    selected.push($scope.slide.options.resources[i]);
                  }
                }
              }

              $scope.slide.options.resources = selected;
            },
            function error(reason) {
              busService.$emit('log.error', {
                'cause': reason,
                'msg': 'Kunne ikke hente bookings for ressource.'
              });
            }
          );
        }

        // Open the tool.
        $scope.editor.editorOpen = true;
        if (tool.path) {
          $scope.editorURL = tool.path + '?' + window.config.version;
        }
        else {
          $scope.editorURL = 'templates/editors/' + tool.id + '.html?' + window.config.version;
        }
      } else {
        $scope.editor.editorOpen = false;
        $scope.editorURL = '';
      }
    };

    // Setup editor states and functions.
    $scope.editor = {
      editorOpen: false,
      hideEditors: function hideEditors() {
        busService.$emit('bodyService.removeClass', 'is-locked');
        $scope.editor.editorOpen = false;
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
     * Add calendar events from source (for event calendar.)
     */
    $scope.addCalendarEvents = function addCalendarEvents() {
      var arr = [];

      // Process bookings for each resource.
      var addResourceBookings = function addResourceBookings(data) {
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
            busService.$emit('log.error', {
              'cause': reason,
              'msg': 'Kunne ikke hente bookings for ressource.'
            });
          }
        );
      }

      $scope.slide.external_data = arr;
    };

    /**
     * Add a media to $scope.slide.media.
     *
     * @param media
     */
    var addMedia = function (media) {
      var mediaList = [];
      var mediaRemoved = false;

      for (var i = 0; i < $scope.slide.media.length; i++) {
        var element = $scope.slide.media[i];

        if (element.id !== media.id) {
          // Open up for multiple images in slideshow
          if ($scope.slide.slide_type === 'slideshow') {
            mediaList.push(element);
          }
        }
        else {
          mediaRemoved = true;
        }
      }

      if (!mediaRemoved) {
        mediaList.push(media);
      }

      $scope.slide.media = mediaList;
    };

    // Register event listener for select media.
    $scope.$on('mediaOverview.selectMedia', function (event, media) {
      if (media.media_type === 'logo') {
        $scope.slide.logo = media;

        $scope.logoStep = 'logo-picker';
      }
      else {
        addMedia(media);
      }
    });

    // Register event listener for media upload success.
    $scope.$on('mediaUpload.uploadSuccess', function (event, data) {
      mediaFactory.getMedia(data.id).then(
        function success(media) {
          if (media.media_type === 'logo') {
            $scope.slide.logo = media;

            $scope.logoStep = 'logo-picker';
          }
          else {
            if ($scope.slide.slide_type === 'slideshow') {
              $scope.slide.media.push(media);
            }
            else {
              $scope.slide.media = [media];
            }
          }
        },
        function error(reason) {
          busService.$emit('log.error', {
            'cause': reason,
            'msg': 'Kunne ikke tilføje media.'
          });
        }
      );

      var notAllSuccess = data.queue.find(function (item, index) {
        return !item.isSuccess;
      });

      if (!notAllSuccess) {
        $scope.editor.hideEditors();
      }
    });

    $scope.step = 'background-picker';

    /**
     * Change the positioning of two array elements.
     * */
    function swapArrayEntries(arr, firstIndex, lastIndex) {
      var temp = arr[firstIndex];
      arr[firstIndex] = arr[lastIndex];
      arr[lastIndex] = temp;
    }

    /**
     * Push a media right.
     * @param arrowPosition the position of the arrow.
     */
    $scope.pushMediaRight = function pushMediaRight(arrowPosition) {
      if (arrowPosition === $scope.slide.media.length - 1) {
        swapArrayEntries($scope.slide.media, arrowPosition, 0);
      }
      else {
        swapArrayEntries($scope.slide.media, arrowPosition, arrowPosition + 1);
      }
    };

    /**
     * Push a media left.
     * @param arrowPosition the position of the arrow.
     */
    $scope.pushMediaLeft = function pushMediaLeft(arrowPosition) {
      if (arrowPosition === 0) {
        swapArrayEntries($scope.slide.media, arrowPosition, $scope.slide.media.length - 1);
      }
      else {
        swapArrayEntries($scope.slide.media, arrowPosition, arrowPosition - 1);
      }
    };
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
