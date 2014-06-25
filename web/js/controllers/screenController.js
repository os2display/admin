/**
 * Screen controller. Controls the screen creation process.
 */
ikApp.controller('ScreenController', function($scope, $location, $routeParams, screenFactory) {
    /**
     * Scope setup
     */
    $scope.steps = 3;
    $scope.screen = [];
    $scope.groups = [];

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
        $scope.groups = screenFactory.getGroups();
        if (!$routeParams.screenId) {
            // If the ID is not set, get an empty screen.
            $scope.screen = screenFactory.emptyScreen();
            $scope.step = 1;
        } else {
            // Get the step.
            if ($routeParams.step) {
                if ($routeParams.step < 1 || $routeParams.step > $scope.steps) {
                    $location.path('/screen/' + $routeParams.screenId + '/1');
                    return;
                }
                else {
                    $scope.step = $routeParams.step;
                }
            }
            else {
                $scope.step = 1;
            }

            // Get screen.
            $scope.screen = screenFactory.getScreen($routeParams.screenId);

            if ($scope.screen === null) {
                $location.path('/screen');
                return;
            }

            // Make sure we are not placed at steps later than what is set in the data.
            var s = 1;
            if ($scope.screen.title !== '') {
                s = s + 1;
                if ($scope.screen.orientation !== '') {
                    s = s + 1;
                }
            }
            if ($scope.step > s) {
                $location.path('/screen/' + $scope.screen.id + '/' + s);
                return;
            }
        }
    }
    init();

    /**
     * Submit a step in the installation process.
     */
    $scope.submitStep = function() {
        $scope.screen = screenFactory.saveScreen($scope.screen);

        if ($scope.step < $scope.steps) {
            $location.path('/screen/' + $scope.screen.id + '/' + (parseInt($scope.step) + 1));
        } else {
            $location.path('/screens');
        }
    }

    /**
     * Set the orientation of the screen.
     * @param orientation
     */
    $scope.setOrientation = function(orientation) {
        $scope.screen.orientation = orientation;
    }

    /**
     * Validates that @field is not empty on screen.
     */
    function validateNotEmpty(field) {
        if (!$scope.screen) {
            return false;
        }
        return $scope.screen[field] !== '';
    }

    /**
     * Find the groups screen with @id is part of
     * @param Screen id
     * @param Group id
     * @returns boolean
     */
    $scope.isScreenInGroup = function(screen_id, group_id) {
        var screen_id = parseInt(screen_id);
        var group = screenFactory.getGroup(group_id);
        if (group.screens.indexOf(screen_id) != -1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Toogle if screen is part group
     * @param screen_id
     * @param group_id
     */

    $scope.toggleGroup = function(screen_id, group_id) {
        if($scope.isScreenInGroup(screen_id, group_id)) {
            screenFactory.removeScreenFromGroup(screen_id, group_id);
        } else {
            screenFactory.addScreenToGroup(screen_id, group_id);
        }
    }

    $scope.openToolbar = function(toolbar) {
        alert(toolbar);
    }

    /**
     * Handles the validation of the data in the screen.
     */
    $scope.validation = {
        titleSet: function() {
            return validateNotEmpty('title');
        },
        dimensionSet: function() {
            return validateNotEmpty('width') && validateNotEmpty('height');
        }
    };

});