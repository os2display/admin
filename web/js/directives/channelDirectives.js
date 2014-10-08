/**
 * @file
 * Contains channel directives.
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
ikApp.directive('ikChannel', ['$interval', '$location', 'channelFactory', 'slideFactory', 'templateFactory',
  function($interval, $location, channelFactory, slideFactory, templateFactory) {
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

        /**
         * Set the template, current image and update the style.
         */
        scope.setTemplate = function() {
          scope.ikSlide = scope.slides[scope.slideIndex];
          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

          var template = templateFactory.getTemplate(scope.ikSlide.template);

          scope.ikSlide.currentImage = '';

          if (scope.ikSlide.options.images && scope.ikSlide.options.images.length > 0) {
            if (scope.ikSlide.imageUrls[scope.ikSlide.options.images[0]] === undefined) {
              scope.ikSlide.currentImage = '/images/not-found.png';
            }
            else {
              scope.ikSlide.currentImage = scope.ikSlide.imageUrls[scope.ikSlide.options.images[0]]['default_landscape_small'];
            }
          }

          if (scope.ikSlide.options.videos && scope.ikSlide.options.videos.length > 0) {
            if (scope.ikSlide.videoUrls[scope.ikSlide.options.videos[0]] === undefined) {
              scope.ikSlide.currentImage = '/images/not-found.png';
            }
            else {
              scope.ikSlide.currentImage = scope.ikSlide.videoUrls[scope.ikSlide.options.videos[0]].thumbnail;
            }
          }

          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(template.idealdimensions.height * parseFloat(scope.ikWidth / template.idealdimensions.width)) + "px",
            fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / template.idealdimensions.width)) + "px"
          }
        };

        // Observe on changes to ik-id, for when it is set.
        attrs.$observe('ikId', function(val) {
          if (!val) {
            return;
          }

          // Load the channel.
          channelFactory.getChannel(val).then(function(data) {
            if (data.slides.length <= 0) {
              scope.templateURL = 'partials/channel/empty.html';
            }
            else {
              scope.channel = data;
              angular.forEach(scope.channel.slides, function(value, key) {
                slideFactory.getSlide(value.id).then(function(data) {
                  scope.slides[key] = (data);
                  if (key === 0) {
                    scope.setTemplate();
                    scope.buttonState = 'play';
                  }
                });
              });
            }
          });
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
            scope.setTemplate();

            scope.interval = $interval(function() {
              scope.setTemplate();

              scope.slideIndex = (scope.slideIndex + 1) % scope.slides.length;
            }, 2000);
            scope.buttonState = 'pause';
          }
        };

        /**
         * Redirect to the channel editor page.
         */
        scope.redirectToChannel = function() {
          if (scope.channel.id) {
            $location.path("/channel/" + scope.channel.id);
          }
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
