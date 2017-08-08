angular.module('toolsModule').directive('slideshowOrderEditor', [
  function () {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
      },
      link: function (scope) {
        /**
         * Change the positioning of two array elements.
         * */
        function swapArrayEntries(arr, firstIndex, lastIndex) {
          var temp = arr[firstIndex];
          arr[firstIndex] = arr[lastIndex];
          arr[lastIndex] = temp;
        }

        /**
         * Push a media right.
         * @param arrowPosition the position of the arrow.
         */
        scope.pushMediaRight = function pushMediaRight(arrowPosition) {
          if (arrowPosition === scope.slide.media.length - 1) {
            swapArrayEntries(scope.slide.media, arrowPosition, 0);
          }
          else {
            swapArrayEntries(scope.slide.media, arrowPosition, arrowPosition + 1);
          }
        };

        /**
         * Push a media left.
         * @param arrowPosition the position of the arrow.
         */
        scope.pushMediaLeft = function pushMediaLeft(arrowPosition) {
          if (arrowPosition === 0) {
            swapArrayEntries(scope.slide.media, arrowPosition, scope.slide.media.length - 1);
          }
          else {
            swapArrayEntries(scope.slide.media, arrowPosition, arrowPosition - 1);
          }
        };

        /**
         * Remove mediaElement from media.
         * @param index
         */
        scope.removeMedia = function removeMedia(index) {
          scope.slide.media.splice(index, 1);
        };

        /**
         * Handle drop media. Move elements around.
         * @param item
         * @param bin
         */
        scope.handleDropMedia = function handleDropMedia(item, bin) {
          item = parseInt(item.split('index-')[1]);
          bin = parseInt(bin.split('index-')[1]);

          var el = scope.slide.media.splice(item, 1);
          scope.slide.media.splice(bin, 0, el[0]);
        };
      },
      templateUrl: '/bundles/os2displaydefaulttemplate/apps/toolsModule/slideshow-order-editor.html'
    };
  }
]);
