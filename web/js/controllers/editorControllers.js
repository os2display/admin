ikApp.controller('LogoEditorController', ['$scope',
  function($scope) {
    $scope.step = 'logo-picker';

    /**
     * Set the step to logo-picker.
     */
    $scope.logoPicker = function logoPicker() {
      $scope.step = 'logo-picker';
    };

    /**
     * Set the step to pick-logo-from-media.
     */
    $scope.pickLogoFromMedia = function pickLogoFromMedia() {
      $scope.step = 'pick-logo-from-media';
      $scope.$emit('mediaOverview.updateSearch');
    };

    /**
     * Set the step to pick-logo-from-computer.
     */
    $scope.pickLogoFromComputer = function pickLogoFromComputer() {
      $scope.step = 'pick-logo-from-computer';
    };
  }
]);

ikApp.controller('BackgroundEditorController', ['$scope',
  function($scope) {
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
  }
]);