/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
angular.module('ikApp').controller('ChannelOverviewController', ['$scope', 'sharedChannelFactory', 'channelFactory', 'configuration',
  function($scope, sharedChannelFactory, channelFactory, configuration) {
    'use strict';

    $scope.shareDialogShow = false;
    $scope.shareDialogChannel = null;

    // If the sharingService is enabled.
    if (configuration.sharingService.enabled) {
      $scope.$on('ikChannelShare.clickShare', function(event, channel) {
        $scope.shareDialogShow = true;
        $scope.shareDialogChannel = channel;

        channelFactory.getChannel(channel.id).then(
          function(data) {
            $scope.shareDialogChannel = data;
            if (!$scope.shareDialogChannel.sharing_indexes) {
              $scope.shareDialogChannel.sharing_indexes = [];
            }
          }
        );
      });

      $scope.sharingIndexes = [];
      sharedChannelFactory.getSharingIndexes().then(function(data) {
        $scope.sharingIndexes = data;
      });

      $scope.saveSharingChannel = function saveSharingChannel() {
        channelFactory.channelShare($scope.shareDialogChannel).then(
          function(data) {},
          function(reason) {}
        );
      };
    }
  }
]);
