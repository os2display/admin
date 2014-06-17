ikApp.directive('contenteditable', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ctrl) {
      // view -> model
      element.bind('blur', function() {
        scope.$apply(function() {
          ctrl.$setViewValue(element.html());
        });
      });

      // model -> view
      ctrl.$render = function() {
        element.html(ctrl.$viewValue);
      };

      // load init value.
      ctrl.$render();
    }
  };
});

ikApp.directive('ikSlide', ['slideFactory', function(slideFactory) {
  return {
    templateUrl: 'partials/slide.html',
    restrict: 'E',
    scope: {},
    link: function(scope, iElement, iAttrs) {
      scope.ikSlide = slideFactory.getSlide(iAttrs.ikId);
      scope.theStyle = {
        width: "" + iAttrs.ikWidth + "px",
        height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(iAttrs.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
        fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(iAttrs.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
      }
    }
  }
}]);

ikApp.directive('ikSlideEditable', ['slideFactory', function(slideFactory) {
  return {
    templateUrl: 'partials/slide-edit.html',
    restrict: 'E',
    scope: {
      slideWidth: '=ikWidth'
    },
    controller: function($scope, imageFactory) {
      /**
       * Sets the images from the factory.
       */
      $scope.backgroundImages = imageFactory.getImages();

      /**
       * Handles the state of the editor.
       */
      $scope.editor = {
        showTextEditor: false,
        toggleTextEditor: function() {
          $scope.editor.showBackgroundEditor = false;
          $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
        },
        showBackgroundEditor: false,
        toggleBackgroundEditor: function() {
          $scope.editor.showTextEditor = false;
          $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
        }
      }

      $('.js-ik-slide-editor-fontsize-input').on('keyup', function() {
        $scope.theStyle.fontsize =  "" + parseFloat($scope.ikSlide.options.fontsize * parseFloat($scope.slideWidth / $scope.ikSlide.options.idealdimensions.width)) + "px";
        $scope.$apply();
      });
    },
    link: function(scope, iElement, iAttrs) {
      scope.ikSlide = slideFactory.getSlide(iAttrs.ikId);

      if (scope.ikSlide) {
        scope.theStyle = {
          width: "" + iAttrs.ikWidth + "px",
          height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(iAttrs.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
          fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(iAttrs.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
        }
      }
    }
  }
}]);