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
      scope.templateURL = '/partials/slide/slide-loading.html';
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
                  scope.buttonState = 'play';
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
          scope.buttonState = 'play';
        } else {
          scope.slideIndex = (scope.slideIndex + 1) % scope.slides.length;
          scope.setTemplate();

          scope.interval = $interval(function() {
            scope.setTemplate();

            scope.slideIndex = (scope.slideIndex + 1) % scope.slides.length;
          }, 2000);
          scope.buttonState = 'pause';
        }
      }
    },
    template: '<div class="preview--channel"><div data-ng-include="" src="templateURL" class="preview--channel-display"></div><div class="preview--channel-{{buttonState}}" ng-click="play()"></div></div>'
  }
}]);
