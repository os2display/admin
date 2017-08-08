angular.module('toolsModule').directive('backgroundEditor', [
  'mediaFactory', function (mediaFactory) {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
      },
      link: function (scope) {
        scope.step = 'background-picker';

        /**
         * Set the step to background-picker.
         */
        scope.backgroundPicker = function backgroundPicker() {
          scope.step = 'background-picker';
        };

        /**
         * Set the step to pick-from-media.
         */
        scope.pickFromMedia = function pickFromMedia() {
          scope.step = 'pick-from-media';
        };

        /**
         * Set the step to pick-from-computer.
         */
        scope.pickFromComputer = function pickFromComputer() {
          scope.step = 'pick-from-computer';
        };

        /**
         * When clicking the background color button,
         * remove selected images.
         */
        scope.clickBackgroundColor = function clickBackgroundColor() {
          scope.slide.options.images = [];
        };

        /**
         * Add a media to scope.slide.media.
         *
         * @param media
         */
        var addMedia = function (media) {
          var mediaList = [];
          var mediaRemoved = false;

          for (var i = 0; i < scope.slide.media.length; i++) {
            var element = scope.slide.media[i];

            if (element.id === media.id) {
              mediaRemoved = true;
            }
          }

          if (!mediaRemoved) {
            mediaList.push(media);
          }

          scope.slide.media = mediaList;
        };

        // Register event listener for select media.
        scope.$on('mediaOverview.selectMedia', function (event, media) {
          addMedia(media);
        });

        // Register event listener for media upload success.
        scope.$on('mediaUpload.uploadSuccess', function (event, data) {
          mediaFactory.getMedia(data.id).then(
            function success(media) {
              scope.slide.media = [media];
            },
            function error(reason) {
              busService.$emit('log.error', {
                'cause': reason,
                'msg': 'Kunne ikke tilføje media.'
              });
            }
          );

          var notAllSuccess = data.queue.find(function (item, index) {
            return !item.isSuccess;
          });

          if (!notAllSuccess) {
            scope.close();
          }
        });

      },
      templateUrl: '/bundles/os2displaydefaulttemplate/apps/toolsModule/background-editor.html'
    };
  }
]);
