ikApp.directive('ikChannel', ['$interval', 'channelFactory', 'slideFactory', function($interval, channelFactory, slideFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      attrs.$observe('ikId', function(val) {
        scope.templateURL = '';

        scope.channel = [];
        channelFactory.getChannel(val).then(function(data) {
          scope.channels = data;
          scope.slideIndex = 0;

          var interval = $interval(function() {
            slideFactory.getSlide(scope.channels.slides[scope.slideIndex]).then(function(data) {
              scope.ikSlide = data;
              scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

              scope.theStyle = {
                width: "" + scope.ikWidth + "px",
                height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
                fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
              }
            });

            scope.slideIndex = (scope.slideIndex + 1) % scope.channels.slides.length;
          }, 5000);
        });
      });
    },
    template: '<div data-ng-include="" src="templateURL"></div>'
  }
}]);
