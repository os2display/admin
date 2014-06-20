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

ikApp.directive('ikScreen', ['screenFactory', function(screenFactory) {
  return {
    templateUrl: 'partials/screen.html',
    restrict: 'E',
    scope: {},
    link: function(scope, element, attrs) {
      scope.ikScreen = screenFactory.getScreen(attrs.ikId);
    }
  }
}]);

ikApp.directive('ikSlide', ['slideFactory', 'templateFactory', function(slideFactory, templateFactory) {
  return {
    restrict: 'E',
    scope: {},
    controller: function($scope) {
      this.getSlide = function() {
        return $scope.ikSlide;
      }
    },
    link: function(scope, element, attrs, controller) {
      scope.ikSlide = slideFactory.getSlide(attrs.ikId);

      scope.templateURL = (templateFactory.getTemplate(scope.ikSlide.template)).html;
      scope.theStyle = {
        width: "" + attrs.ikWidth + "px",
        height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(attrs.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
        fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(attrs.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
      }
    },
    template: '<div data-ng-include="" src="templateURL"></div>'
  }
}]);

ikApp.directive('ikSlideEditable', ['slideFactory', function(slideFactory) {
  return {
    restrict: 'E',
/*    require: 'ikSlide',
    scope: {
      sid: '=ikId',
      swidth: '=ikWidth'
    },
    controller: function($scope, imageFactory) {
      // Sets the images from the factory.
      $scope.backgroundImages = imageFactory.getImages();

      // Editor states and functions to toggle menues.
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
    },
    link: function(scope, element, attrs) {
      // Add keyup event listener for fontsize, to make sure the preview updates font size.
      iElement.find('.js-ik-slide-editor-fontsize-input').on('keyup', function() {
        scope.theStyle.fontsize =  "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.slideWidth / scope.ikSlide.options.idealdimensions.width)) + "px";
        scope.$apply();
      });

      // Cleanup.
      iElement.on('$destroy', function() {
        $(this).find('.js-ik-slide-editor-fontsize-input').off('keyup');
      });
    },*/
    templateUrl: '/partials/slide-edit.html'
  }
}]);