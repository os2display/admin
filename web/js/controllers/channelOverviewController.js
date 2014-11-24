/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
ikApp.controller('ChannelOverviewController', ['$scope', 'sharedChannelFactory', 'channelFactory',
  function($scope, sharedChannelFactory, channelFactory) {
    $scope.shareDialogShow = false;
    $scope.shareDialogChannel = null;

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
]);
