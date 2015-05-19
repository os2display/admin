/**
 * @file
 * Channel creation controllers.
 */

/**
 * Channel controller. Controls the channel creation process.
 */
angular.module('ikApp').controller('ChannelController', ['$scope', '$location', '$routeParams', '$timeout', 'channelFactory', 'slideFactory', 'itkLogFactory',
  function ($scope, $location, $routeParams, $timeout, channelFactory, slideFactory, itkLogFactory) {
    'use strict';

    $scope.steps = 3;
    $scope.slides = [];
    $scope.channel = {};

    // Get all slides.
    slideFactory.getSlides().then(
      function (data) {
        $scope.slides = data;
      },
      function error(reason) {
        itkLogFactory.error("Hentning af slides fejlede", reason);
      }
    );

    // Setup the editor.
    $scope.editor = {
      slideOverviewEditor: false,
      toggleSlideOverviewEditor: function () {
        $('html').toggleClass('is-locked');
        $scope.editor.slideOverviewEditor = !$scope.editor.slideOverviewEditor;
      }
    };

    // Register event listener for clickSlide.
    $scope.$on('slideOverview.clickSlide', function (event, slide) {
      $scope.toggleSlide(slide);
    });

    /**
     * Loads a given step.
     */
    function loadStep(step) {
      $scope.step = step;
      $scope.templatePath = '/app/pages/channel/channel-step' + $scope.step + '.html';
    }

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
      if (!$routeParams.id) {
        // If the ID is not set, get an empty channel.
        $scope.channel = channelFactory.emptyChannel();
        loadStep(1);
      } else {
        if ($routeParams.id == null || $routeParams.id == undefined || $routeParams.id == '') {
          $location.path('/channel');
        }
        else {
          channelFactory.getEditChannel($routeParams.id).then(
            function (data) {
              $scope.channel = data;
              $scope.channel.status = 'edit-channel';

              if ($scope.channel === {}) {
                $location.path('/channel');
              }

              // Go to add slides page.
              loadStep(2);
            },
            function error(reason) {
              $location.path('/channel-overview');
              itkLogFactory.error("Hentning af valgt kanal med id:" + $routeParams.id + " fejlede", reason);
            }
          );
        }
      }
    }

    init();

    /**
     * Submit a step in the installation process.
     */
    $scope.submitStep = function () {
      if ($scope.step == $scope.steps) {
        $scope.disableSubmitButton = true;

        channelFactory.saveChannel().then(
          function success() {
            itkLogFactory.info("Kanal gemt.", 3000);
            $timeout(function () {
              $location.path('/channel-overview');
            }, 1000);
          },
          function error(reason) {
            itkLogFactory.error("Gem af kanal fejlede.", reason);
            $scope.disableSubmitButton = false;
          }
        );
      } else {
        loadStep($scope.step + 1);
      }
    };

    /**
     * Set the orientation of the channel.
     * @param orientation
     */
    $scope.setOrientation = function setOrientation(orientation) {
      $scope.channel.orientation = orientation;
    };

    /**
     * Is the slide selected?
     * @param id
     * @returns {boolean}
     */
    $scope.slideSelected = function slideSelected(id) {
      var res = false;

      $scope.channel.slides.forEach(function (element) {
        if (id == element.id) {
          res = true;
        }
      });

      return res;
    };

    /**
     * Validates that @field is not empty on channel.
     */
    function validateNotEmpty(field) {
      if (!$scope.channel) {
        return false;
      }
      return $scope.channel[field] !== '';
    }

    /**
     * Handles the validation of the data in the channel.
     */
    $scope.validation = {
      titleSet: function () {
        return validateNotEmpty('title');
      },
      orientationSet: function () {
        return validateNotEmpty('orientation');
      }
    };

    /**
     * Select or deselect the slides related to a channel.
     * @param slide
     */
    $scope.toggleSlide = function toggleSlide(slide) {
      var res = null;

      $scope.channel.slides.forEach(function (element, index, array) {
        if (slide.id == element.id) {
          res = index;
        }
      });

      if (res !== null) {
        $scope.channel.slides.splice(res, 1);
      }
      else {
        $scope.channel.slides.push(slide);
      }
    };

    /**
     * Change channel creation step.
     * @param step
     */
    $scope.goToStep = function goToStep(step) {
      var s = 1;
      // If title is set enable next step.
      if ($scope.validation.titleSet()) {
        s++;
        // If orientation is set enable next three steps.
        if ($scope.validation.orientationSet()) {
          s = s + 3;
        }
      }
      if (step <= s) {
        loadStep(step);
      }
    };

    /**
     * Change the positioning of two array elements.
     * */
    function swapArrayEntries(arr, firstIndex, lastIndex) {
      var temp = arr[firstIndex];
      arr[firstIndex] = arr[lastIndex];
      arr[lastIndex] = temp;
    }

    /**
     * Push a channel slide right.
     * @param arrowPosition the position of the arrow.
     */
    $scope.pushRight = function pushRight(arrowPosition) {
      if (arrowPosition == $scope.channel.slides.length - 1) {
        swapArrayEntries($scope.channel.slides, arrowPosition, 0);
      }
      else {
        swapArrayEntries($scope.channel.slides, arrowPosition, arrowPosition + 1);
      }
    };

    /**
     * Push a channel slide right.
     * @param arrowPosition the position of the arrow.
     */
    $scope.pushLeft = function pushLeft(arrowPosition) {
      if (arrowPosition == 0) {
        swapArrayEntries($scope.channel.slides, arrowPosition, $scope.channel.slides.length - 1);
      }
      else {
        swapArrayEntries($scope.channel.slides, arrowPosition, arrowPosition - 1);
      }
    };
  }
]);