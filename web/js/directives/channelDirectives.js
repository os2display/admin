ikApp.directive('ikChannel', ['$interval', 'channelFactory', 'slideFactory', function($interval, channelFactory, slideFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      scope.slideIndex = 0;

      scope.setTemplate = function(slideID) {
        console.log(scope.channel.slides);
        slideFactory.getSlide(scope.channel.slides[slideID]).then(function(data) {
          scope.ikSlide = data;
          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
            fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
          }
        });
      }

      attrs.$observe('ikId', function(val) {
        scope.templateURL = '';

        scope.channel = [];
        channelFactory.getChannel(val).then(function(data) {
          scope.channel = data;

          scope.setTemplate(scope.channel.slides[0]);
        });
      });

      scope.play = function() {
        if (angular.isDefined(scope.interval)) {
          $interval.cancel(scope.interval);
          scope.interval = undefined;
        }

        scope.interval = $interval(function() {
          scope.setTemplate(scope.channel.slides[scope.slideIndex]);

          scope.slideIndex = (scope.slideIndex + 1) % scope.channel.slides.length;
        }, 10000);
      }
    },
    template: '<div data-ng-include="" src="templateURL"></div><div class="play" ng-click="play()">PLAY</div>'
  }
}]);
