/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
angular.module('ikApp').controller('ChannelOverviewController', ['$scope', 'sharedChannelFactory', 'channelFactory', 'configuration', 'itkLogFactory',
  function($scope, sharedChannelFactory, channelFactory, configuration, itkLogFactory) {
    'use strict';

    $scope.shareDialogShow = false;
    $scope.shareDialogChannel = null;

    // If the sharingService is enabled.
    if (configuration.sharingService.enabled) {
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
            itkLogFactory.error('Hentning af kanal fejlede', reason);
          }
        );
      });

      $scope.sharingIndexes = [];
      sharedChannelFactory.getSharingIndexes().then(
        function success(data) {
          $scope.sharingIndexes = data;
        },
        function error(reason) {
          itkLogFactory.error('Hentning af delingsindeks fejlede.', reason);
        }
      );

      $scope.saveSharingChannel = function saveSharingChannel() {
        channelFactory.channelShare($scope.shareDialogChannel).then(
          function() {
            itkLogFactory.info('Delingskonfiguration af kanal lykkedes.', 3000);
          },
          function(reason) {
            itkLogFactory.error('Deling af kanal fejlede.', reason);
          }
        );
      };
    }
  }
]);
