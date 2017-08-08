angular.module('templateModule').directive('logoEditor', [
  'mediaFactory', function (mediaFactory) {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
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

        /**
         * Available logo positions.
         */
        scope.logoPositions = [
          {
            value: {
              'top': '0',
              'bottom': 'auto',
              'left': '0',
              'right': 'auto'
            },
            text: 'top venstre'
          },
          {
            value: {
              'top': '0',
              'bottom': 'auto',
              'left': 'auto',
              'right': '0'
            },
            text: 'top højre'
          },
          {
            value: {
              'top': 'auto',
              'bottom': '0',
              'left': '0',
              'right': 'auto'
            },
            text: 'bund venstre'
          },
          {
            value: {
              'top': 'auto',
              'bottom': '0',
              'left': 'auto',
              'right': '0'
            },
            text: 'bund højre'
          }
        ];

        /**
         * Available logo sizes.
         */
        scope.logoSizes = [
          {
            value: "5%",
            text: "Meget lille (5% af skærmen)"
          },
          {
            value: "10%",
            text: "Lille (10% af skærmen)"
          },
          {
            value: "15%",
            text: "Medium (15% af skærmen)"
          },
          {
            value: "20%",
            text: "Stor (20% af skærmen)"
          },
          {
            value: "40%",
            text: "Ekstra stor (40% af skærmen)"
          }
        ];

        // Register event listener for select media.
        scope.$on('mediaOverview.selectMedia', function (event, media) {
          if (media.media_type === 'logo') {
            scope.slide.logo = media;

            scope.logoStep = 'logo-picker';
          }
        });

        // Register event listener for media upload success.
        scope.$on('mediaUpload.uploadSuccess', function (event, data) {
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
      templateUrl: '/bundles/os2displaydefaulttemplate/apps/templateModule/logo-editor.html'
    };
  }
]);
