ikApp.directive('ikChannel', ['channelFactory', 'slideFactory', function(channelFactory, slideFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      scope.templateURL = 'partials/slide-loading.html';

      attrs.$observe('ikId', function(val) {
        scope.channel = [];
        channelFactory.getChannel(val).then(function(data) {
          scope.channel = data;

          var interval = $interval(function() {
            slideFactory.getSlide(scope.ikId).then(function(data) {
              scope.ikSlide = data;
              scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

              scope.theStyle = {
                width: "" + scope.ikWidth + "px",
                height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
                fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
              }
            });
          }, 5000);
        });
      });
    },
    template: '<div data-ng-include="" src="templateURL" include-replace></div>'
  }
}]);
