/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
ikApp.controller('ChannelOverviewController', ['$scope', 'sharedChannelFactory',
  function($scope, sharedChannelFactory) {
    $scope.shareDialogShow = false;
    $scope.shareDialogChannel = null;

    $scope.$on('ikChannelShare.clickShare', function(event, channel) {
      $scope.shareDialogShow = true;
      $scope.shareDialogChannel = channel;
    });

    $scope.sharedIndexes = sharedChannelFactory.getSharingIndexes();
  }
]);
