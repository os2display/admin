/**
 * @file
 * Slide creation controllers.
 */

/**
 * Slide controller. Controls the slide creation/edit process.
 */
ikApp.controller('SlideController', ['$scope', '$location', '$routeParams', '$timeout', 'slideFactory', 'templateFactory', 'channelFactory',
  function($scope, $location, $routeParams, $timeout, slideFactory, templateFactory, channelFactory) {
    $scope.steps = 6;
    $scope.slide = {};
    $scope.templates = [];
    templateFactory.getTemplates().then(
      function(data) {
        $scope.templates = data;
      }
    );
    $scope.channels = [];

    /**
     * Load a given step
     */
    function loadStep(step) {
      $scope.step = step;
      $scope.templatePath = '/partials/slide/slide' + $scope.step + '.html';
    }

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
      // Get all channels for step 6
      channelFactory.getChannels().then(function(data) {
        $scope.channels = data;
      });

      if (!$routeParams.id) {
        // If the ID is not set, get an empty slide.
        $scope.slide = slideFactory.emptySlide();
        $scope.slide.channels = [];
        loadStep(1);
      }
      else {
        if ($routeParams.id == null || $routeParams.id == undefined || $routeParams.id == '') {
          $location.path('/slide');
        }
        else {
          // Make sure we load a fresh version of the slide.
          slideFactory.clearCurrentSlide();

          // Get the slide from the backend.
          slideFactory.getEditSlide($routeParams.id).then(
            function(data) {
              $scope.slide = data;
              $scope.slide.status = 'edit-slide';
              if ($scope.slide === {}) {
                $location.path('/slide');
              }

              loadStep(4);
            },
            function(reason) {
              $location.path('/slide-overview');
            }
          );
        }
      }
    }
    init();

    /**
     * Submit a step in the installation process.
     */
    $scope.submitStep = function() {
      if ($scope.step == $scope.steps) {
        $scope.disableSubmitButton = true;

        // Set default duration if none is set.
        if ($scope.slide.duration == '') {
          $scope.slide.duration = 15;
        }

        slideFactory.saveSlide().then(
          function() {
            $timeout(function() {
              $location.path('/slide-overview');
            }, 1000)
          },
          function() {
            $scope.disableSubmitButton = false;
          }
        );
      }
      else {
        loadStep($scope.step + 1);
      }
    };

    /**
     * Validates that @field is not empty on slide.
     */
    function validateNotEmpty(field) {
      if (!$scope.slide) {
        return false;
      }
      return $scope.slide[field] !== '';
    }

    /**
     * Handles the validation of the data in the slide.
     */
    $scope.validation = {
      titleSet: function() {
        return validateNotEmpty('title');
      },
      orientationSet: function() {
        return validateNotEmpty('orientation');
      },
      templateSet: function() {
        return validateNotEmpty('template');
      }
    };

    /**
     * Go the given step in the creation process, if the requirements have been met to be at that step.
     * @param step
     */
    $scope.goToStep = function(step) {
      var s = 1;
      if ($scope.validation.titleSet()) {
        s++;
        if ($scope.validation.orientationSet()) {
          s++;
          if ($scope.validation.templateSet()) {
            s = s + 3;
          }
        }
      }
      if (step <= s) {
        loadStep(step);
      }
    };

    /**
     * Set the template id of a slide.
     * Update the options attribute to add fields that are needed for the template.
     *
     * @param id
     */
    $scope.selectTemplate = function(id) {
      $scope.slide.template = id;

      var template = $scope.templates[id];

      if (template === null) {
        return;
      }

      if ($scope.slide.options == null) {
        $scope.slide.options = template.emptyoptions;
      }
      else {
        angular.forEach(template.emptyoptions, function(value, key)  {
          if ($scope.slide.options[key] == undefined) {
            $scope.slide.options[key] = value;
          }
        });
      }
      if ($scope.slide.options.headline !== undefined && $scope.slide.options.headline == '') {
        $scope.slide.options.headline = $scope.slide.title;
      }

      $scope.slide.media_type = template.mediatype;
    };

    /**
     * Set the orientation of the slide.
     * @param orientation
     */
    $scope.selectOrientation = function(orientation) {
      $scope.slide.orientation = orientation;
      $scope.slide.template = '';
    };

    /**
     * Is the channel selected?
     * @param channel
     * @returns {boolean}
     */
    $scope.channelSelected = function(channel) {
      var res = false;

      $scope.slide.channels.forEach(function(slideChannel) {
        if (channel.id == slideChannel.id) {
          res = true;
        }
      });

      return res;
    };

    /**
     * Add remove a channel.
     * @param channel
     */
    $scope.toggleChannel = function(channel) {
      var index = null;

      $scope.slide.channels.forEach(function(slideChannel, channelIndex) {
        if (channel.id == slideChannel.id) {
          index = channelIndex;
        }
      });

      if (index !== null) {
        $scope.slide.channels.splice(index, 1);
      }
      else {
        $scope.slide.channels.push(channel);
      }
    }
  }
]);