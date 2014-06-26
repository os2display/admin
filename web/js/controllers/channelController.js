/**
 * Channel controller. Controls the channel creation process.
 */
ikApp.controller('ChannelController', function($scope, $location, $routeParams, channelFactory, slideFactory) {
  /**
   * Scope setup
   */
  $scope.steps = 4; // Number of steps in the creation process.
  $scope.slides = [];

  slideFactory.getSlides().then(function(data) {
    $scope.slides = data;
  });

  /**
   * Constructor.
   * Handles different settings of route parameters.
   */
  function init() {
    if (!$routeParams.channelId) {
      // If the ID is not set, get an empty channel.
      $scope.channel = channelFactory.emptyChannel();
      $scope.step = 1;
    } else {
      // Get the step.
      if ($routeParams.step) {
        if ($routeParams.step < 1 || $routeParams.step > $scope.steps) {
          $location.path('/channel/' + $routeParams.channelId + '/1');
          return;
        }
        else {
          $scope.step = $routeParams.step;
        }
      }
      else {
        $scope.step = 1;
      }

      // Get channel.
      //$scope.channel = channelFactory.getChannel($routeParams.channelId);

      if ($scope.channel === null) {
        $location.path('/channel');
        return;
      }

      // Make sure we are not placed at steps later than what is set in the data.
      var s = 1;
      if ($scope.channel.title !== '') {
        s = s + 1;
        if ($scope.channel.orientation !== '') {
          s = s + 1;
          if ($scope.channel.slide !== '') {
            s = s + 1;
          }
        }
      }
      if ($scope.step > s) {
        $location.path('/channel/' + $scope.channel.id + '/' + s);
        return;
      }
    }
  }
  init();


  /**
   * Submit a step in the installation process.
   */
  $scope.submitStep = function() {
    $scope.channel = channelFactory.saveChannel($scope.channel);

    // Modify history to make sure the back button does not redirect to #/channel/, so a new channel will be created.
    if ($scope.step == 1) {
      window.history.replaceState({}, "", "#/channel/" + $scope.channel.id + "/1");
    }

    if ($scope.step < $scope.steps) {
      $location.path('/channel/' + $scope.channel.id + '/' + (parseInt($scope.step) + 1));
    } else {
      $location.path('/channels');
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
    var slidesArray = [];

    if (!$scope.channel) {
      return slidesArray;
    }

    angular.forEach($scope.channel.slides, function(id, index){
      slidesArray.push(slideFactory.getSlide(id));
    });
    return slidesArray;
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
  };


  /**
   * Push a channel slide right.
   * @param index the position of the arrow.
   */
  $scope.pushLeft = function($arrow_position) {
    reorderIndex($scope.channel.slides, $arrow_position, $arrow_position - 1);
  };
});