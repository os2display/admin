/**
 * Channel controller. Controls the channel creation process.
 */
ikApp.controller('ChannelController', function($scope, $location, $routeParams, channelFactory, slideFactory) {
    /**
     * Scope setup
     */
    $scope.steps = 4; // Number of steps in the creation process.
    $scope.channel = null; // Channel created in the process.
    $scope.slides = slideFactory.getSlides(); // All available slides.

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
            $scope.channel = channelFactory.getChannel($routeParams.channelId);

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

    $scope.openToolbar = function(toolbar) {
        alert(toolbar);
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
        $scope.channel.slides[id] = id;
      }
      else {
        delete $scope.channel.slides[id];
      }
      console.log($scope.channel);
    }
});