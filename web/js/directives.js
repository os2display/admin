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