angular.module('toolsModule').directive('logoPicker', [
  'mediaFactory', function (mediaFactory) {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&',
        template: '@'
      },
      link: function (scope) {
        scope.logoStep = 'logo-picker';

        /**
         * Set the step to logo-picker.
         */
        scope.logoPicker = function logoPicker() {
          scope.logoStep = 'logo-picker';
        };

        /**
         * Set the step to pick-logo-from-media.
         */
        scope.pickLogoFromMedia = function pickLogoFromMedia() {
          scope.logoStep = 'pick-logo-from-media';
          scope.$emit('mediaOverview.updateSearch');
        };

        /**
         * Set the step to pick-logo-from-computer.
         */
        scope.pickLogoFromComputer = function pickLogoFromComputer() {
          scope.logoStep = 'pick-logo-from-computer';
        };

        // Register event listener for select media.
        scope.$on('mediaOverview.selectMedia', function (_, media) {
          if (media.media_type === 'logo') {
            scope.slide.logo = media;

            scope.logoStep = 'logo-picker';
          }
        });

        // Register event listener for media upload success.
        scope.$on('mediaUpload.uploadSuccess', function (_, data) {
          mediaFactory.getMedia(data.id).then(
            function success(media) {
              if (media.media_type === 'logo') {
                scope.slide.logo = media;

                scope.logoStep = 'logo-picker';
              }
            },
            function error(reason) {
              busService.$emit('log.error', {
                'cause': reason,
                'msg': 'Kunne ikke tilføje media.'
              });
            }
          );
        });
      },
      templateUrl: function(_, attrs) {
        return attrs.template ? attrs.template : '/bundles/kkbding2integration/apps/dingEditors/logo-picker.html';
      }
    };
  }
]);
