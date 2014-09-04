/**
 * Channel controller. Controls the channel creation process.
 */
ikApp.controller('ChannelController', function($scope, $location, $routeParams, $timeout, channelFactory, slideFactory, screenFactory) {
  $scope.steps = 5;
  $scope.slides = [];
  $scope.channel = {};
  $scope.slidesArray = [];
  $scope.screens = [];

  // Get all screens.
  screenFactory.getScreens().then(function(data) {
    $scope.screens = data;
  });

  // Get all slides.
  slideFactory.getSlides().then(function(data) {
    $scope.slides = data;
  });

  /**
   * Loads a given step.
   */
  function loadStep(step) {
    $scope.step = step;
    $scope.templatePath = '/partials/channel/channel' + $scope.step + '.html';
    if ($scope.step == 4) {
      $scope.getChosenSlides();
    }
  };

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
        channelFactory.getEditChannel($routeParams.id).then(function(data) {
          $scope.channel = data;
          $scope.channel.status = 'edit-channel';

          if ($scope.channel === {}) {
            $location.path('/channel');
          }

          loadStep($scope.steps);
        });
      }
    }
  };
  init();

  /**
   * Submit a step in the installation process.
   */
  $scope.submitStep = function() {
    if ($scope.step == $scope.steps) {
      channelFactory.saveChannel().then(function() {
        $timeout(function() {
          $location.path('/channel-overview');
        }, 500);
      });
    } else {
      loadStep($scope.step + 1);
    }
  };

  /**
   * Set the orientation of the channel.
   * @param orientation
   */
  $scope.setOrientation = function(orientation) {
    $scope.channel.orientation = orientation;
  };

  /**
   * Is the screen selected?
   * @param screen
   * @returns {boolean}
   */
  $scope.screenSelected = function(id) {
    var res = false;

    $scope.channel.screens.forEach(function(element, index, array) {
      if (id == element.id) {
        res = true;
      };
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
  };

  /**
   * Handles the validation of the data in the channel.
   */
  $scope.validation = {
    titleSet: function() {
      return validateNotEmpty('title');
    },
    orientationSet: function() {
      return validateNotEmpty('orientation');
    }
  };

  /**
   * Select or deselect the slides related to a channel.
   * @param id
   */
  $scope.toggleSlide = function(id) {
    if($scope.channel.slides.indexOf(id) < 0) {
      $scope.channel.slides.push(id);
    }
    else {
      $scope.channel.slides.splice($scope.channel.slides.indexOf(id), 1);
    }
  };

  /**
   * Select or deselect the screens related to a channel.
   * @param id
   */
  $scope.toggleScreen = function(screen) {
    var res = false;

    $scope.channel.screens.forEach(function(element, index, array) {
      if (screen.id == element.id) {
        res = true;
      };
    });

    if (res) {
      $scope.channel.screens.splice($scope.channel.screens.indexOf(screen), 1);
    }
    else {
      $scope.channel.screens.push(screen);
    }
  }

  /**
   * Change channel creation step.
   * @param step
   */
  $scope.goToStep = function(step) {
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
   * Fetch the slides related to the channel.
   */
  $scope.getChosenSlides = function() {
    $scope.slidesArray.length = 0;
    angular.forEach($scope.channel.slides, function(id, index){
      slideFactory.getSlide(id).then(function(data) {
        $scope.slidesArray[index] = data;
      });
    });
  }

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
   * @param index the position of the arrow.
   */
  $scope.pushRight = function(arrowPosition) {
    if (arrowPosition == $scope.channel.slides.length - 1) {
      swapArrayEntries($scope.channel.slides, arrowPosition, 0);
      swapArrayEntries($scope.slidesArray, arrowPosition, 0);
    }
    else {
      swapArrayEntries($scope.channel.slides, arrowPosition, arrowPosition + 1);
      swapArrayEntries($scope.slidesArray, arrowPosition, arrowPosition + 1);
    }
  };

  /**
   * Push a channel slide right.
   * @param index the position of the arrow.
   */
  $scope.pushLeft = function(arrowPosition) {
    if (arrowPosition == 0) {
      swapArrayEntries($scope.channel.slides, arrowPosition, $scope.channel.slides.length - 1);
      swapArrayEntries($scope.slidesArray, arrowPosition, $scope.channel.slides.length - 1);
    }
    else {
      swapArrayEntries($scope.channel.slides, arrowPosition, arrowPosition - 1);
      swapArrayEntries($scope.slidesArray, arrowPosition, arrowPosition - 1);
    }
  };
});