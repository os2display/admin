/**
 * Channel controller. Controls the channel creation process.
 */
ikApp.controller('ChannelController', function($scope, $location, $routeParams, channelFactory, slideFactory) {
  $scope.steps = 4; // Number of steps in the creation process.
  $scope.slides = [];
  $scope.channel = {};
  var slidesArray = [];

  slideFactory.getSlides().then(function(data) {
    $scope.slides = data;
  });

  /**
   * Loads a given step
   */
  function loadStep(step) {
    $scope.step = step;
    $scope.templatePath = '/partials/channel' + $scope.step + '.html';
    if ($scope.step == 4) {
      $scope.getChosenSlides();
    }
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
        channelFactory.getEditChannel($routeParams.id).then(function(data) {
          $scope.channel = data;

          if ($scope.channel === {}) {
            $location.path('/channel');
          }

          loadStep($scope.steps);
        });
      }
    }
  }
  init();

  /**
   * Submit a step in the installation process.
   */
  $scope.submitStep = function() {
    if ($scope.step == $scope.steps) {
      channelFactory.saveChannel().then(function() {
        $location.path('/channels');
      });
    } else {
      loadStep($scope.step + 1);
    }
  }


  /**
   * Set the orientation of the channel.
   * @param orientation
   */
  $scope.setOrientation = function(orientation) {
    $scope.channel.orientation = orientation;
  }


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
    titleSet: function() {
      return validateNotEmpty('title');
    },
    orientationSet: function() {
      return validateNotEmpty('orientation');
    },
    slideSet: function() {
      return validateNotEmpty('slide');
    }
  };


  /**
   * Select or deselect the slides related to a channel.
   * @param id
   */
  $scope.toggleSlide = function(id) {
    if($scope.channel.slides.indexOf(id)==-1) {
      $scope.channel.slides.push(id);
    }
    else {
      $scope.channel.slides.splice($scope.channel.slides.indexOf(id), 1);
    }
  }


  /**
   * Fetch the slides related to the channel.
   */
  $scope.getChosenSlides = function() {
    $scope.slidesArray = [];
    angular.forEach($scope.channel.slides, function(id, index){
      slideFactory.getSlide(id).then(function(data) {
        $scope.slidesArray.push(data);
      })
    });
  }


  /**
   * Change the positioning of two array elements.
   * */
  function reorderIndex(arr, old_index, new_index) {
    // If it's not the last element.
    if (new_index < arr.length) {
      // Change index order.
      arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
    }
  }


  /**
   * Push a channel slide right.
   * @param index the position of the arrow.
   */
  $scope.pushRight = function($arrow_position) {
    reorderIndex($scope.channel.slides, $arrow_position, $arrow_position + 1);
    reorderIndex($scope.slidesArray, $arrow_position, $arrow_position + 1);
  };


  /**
   * Push a channel slide right.
   * @param index the position of the arrow.
   */
  $scope.pushLeft = function($arrow_position) {
    reorderIndex($scope.channel.slides, $arrow_position, $arrow_position - 1);
    reorderIndex($scope.slidesArray, $arrow_position, $arrow_position - 1);
  };
});