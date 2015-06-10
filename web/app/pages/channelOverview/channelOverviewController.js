/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
angular.module('ikApp').controller('ChannelOverviewController', ['$scope', 'sharedChannelFactory', 'channelFactory', 'itkLog',
  function($scope, sharedChannelFactory, channelFactory, itkLog) {
    'use strict';

    $scope.shareDialogShow = false;
    $scope.shareDialogChannel = null;

    // If the sharingService is enabled.
    if (window.config.sharingService.enabled) {
      $scope.$on('ikChannelShare.clickShare', function(event, channel) {
        $scope.shareDialogShow = true;
        $scope.shareDialogChannel = channel;

        channelFactory.getChannel(channel.id).then(
          function success(data) {
            $scope.shareDialogChannel = data;
            if (!$scope.shareDialogChannel.sharing_indexes) {
              $scope.shareDialogChannel.sharing_indexes = [];
            }
          },
          function error(reason) {
            itkLog.error('Hentning af kanal fejlede', reason);
          }
        );
      });

      $scope.sharingIndexes = [];
      sharedChannelFactory.getSharingIndexes().then(
        function success(data) {
          $scope.sharingIndexes = data;
        },
        function error(reason) {
          itkLog.error('Hentning af delingsindeks fejlede.', reason);
        }
      );

      $scope.saveSharingChannel = function saveSharingChannel() {
        channelFactory.channelShare($scope.shareDialogChannel).then(
          function() {
            itkLog.info('Delingskonfiguration af kanal lykkedes.', 3000);
          },
          function(reason) {
            itkLog.error('Deling af kanal fejlede.', reason);
          }
        );
      };
    }
  }
]);
