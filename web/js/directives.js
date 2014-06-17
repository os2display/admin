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
            iAttrs.$observe('ikId', function(value) {
                scope.ikSlide = slideFactory.getSlide(value);
            });
            iAttrs.$observe('ikWidth', function(value) {
                scope.theStyle = {
                    width: "" + value + "px",
                    height: "" + parseFloat(1080 * parseFloat(value / 1920.0)) + "px",
                    fontsize: "" + parseFloat(32 * parseFloat(value / 1920.0)) + "px"
                }
            });
        }
    }
}]);