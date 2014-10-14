/**
 * @file
 * Contains channel directives.
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
ikApp.directive('ikChannel', ['$interval', '$location',
  function($interval, $location) {
    return {
      restrict: 'E',
      scope: {
        ikWidth: '@',
        ikChannel: '='
      },
      link: function(scope, element, attrs) {
        scope.slideIndex = 0;
        scope.slides = [];
        scope.playText = '';

        // Observe on changes to ik-slide, for when it is set.
        attrs.$observe('ikChannel', function(val) {
          if (!val) {
            return;
          }

          // If channel is empty, display empty channel.
          if (scope.ikChannel.length <= 0) {
            scope.templateURL = 'partials/channel/empty.html';
          }
          else {
            scope.templateURL = 'partials/channel/non-empty.html';

            // Get all the slides from the channel.
            scope.ikChannel.channel_slide_orders.forEach(function(element) {
              scope.slides.push(element.slide);
            });

            scope.buttonState = 'play';
          }
        });

        /**
         * Start playing the slides.
         */
        scope.play = function() {
          if (angular.isDefined(scope.interval)) {
            $interval.cancel(scope.interval);
            scope.interval = undefined;
            scope.buttonState = 'play';
          } else {
            scope.slideIndex = (scope.slideIndex + 1) % scope.slides.length;

            scope.interval = $interval(function() {
              scope.slideIndex = (scope.slideIndex + 1) % scope.slides.length;
            }, 2000);
            scope.buttonState = 'pause';
          }
        };

        /**
         * Redirect to the channel editor page.
         */
        scope.redirectToChannel = function() {
          $location.path("/channel/" + scope.ikChannel.id);
        };

        // Register event listener for destroy.
        //   Cleanup interval.
        scope.$on('$destroy', function() {
          if (angular.isDefined(scope.interval)) {
            $interval.cancel(scope.interval);
            scope.interval = undefined;
          }
        });
      },
      templateUrl: '/partials/channel/channel-template.html'
    }
  }
]);
