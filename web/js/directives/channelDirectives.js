ikApp.directive('ikChannel', ['$interval', 'channelFactory', 'slideFactory', function($interval, channelFactory, slideFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      scope.slideIndex = 0;
      scope.channel = {};
      scope.slides = [];
      scope.templateURL = '';
      scope.playText = '';

      scope.setTemplate = function() {
        scope.ikSlide = scope.slides[scope.slideIndex];
        scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

        scope.theStyle = {
          width: "" + scope.ikWidth + "px",
          height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
          fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
        }
      }

      attrs.$observe('ikId', function(val) {
        channelFactory.getChannel(val).then(function(data) {
          scope.channel = data;
          angular.forEach(scope.channel.slides, function(value, key) {
            slideFactory.getSlide(value).then(function(data) {
              if (data != []) {
                scope.slides.push(data);
                if (key === 0) {
                  scope.setTemplate();
                  scope.playText = 'Play';
                }
              }
            });
          });
        });
      });

      scope.play = function() {
        if (angular.isDefined(scope.interval)) {
          $interval.cancel(scope.interval);
          scope.interval = undefined;
          scope.playText = 'Play';
        } else {
          scope.slideIndex = (scope.slideIndex + 1) % scope.slides.length;
          scope.setTemplate();

          scope.interval = $interval(function() {
            scope.setTemplate();

            scope.slideIndex = (scope.slideIndex + 1) % scope.slides.length;
          }, 2000);
          scope.playText = 'Stop';
        }
      }
    },
    template: '<div class="preview--channel"><div data-ng-include="" src="templateURL" class="preview--channel-display"></div></div><div class="preview--channel-play" ng-click="play()">{{playText}}</div>'
  }
}]);
