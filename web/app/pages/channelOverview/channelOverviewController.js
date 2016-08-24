/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
angular.module('ikApp').controller('ChannelOverviewController', ['$scope', 'sharedChannelFactory', 'channelFactory', 'busService',
  function($scope, sharedChannelFactory, channelFactory, busService) {
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
            busService.$emit('log.error', {
              'cause': reason,
              'msg': 'Hentning af kanal fejlede'
            });
          }
        );
      });

      $scope.sharingIndexes = [];
      sharedChannelFactory.getSharingIndexes().then(
        function success(data) {
          $scope.sharingIndexes = data;
        },
        function error(reason) {
          busService.$emit('log.error', {
            'cause': reason,
            'msg': 'Hentning af delingsindeks fejlede.'
          });
        }
      );

      $scope.saveSharingChannel = function saveSharingChannel() {
        channelFactory.channelShare($scope.shareDialogChannel).then(
          function() {
            busService.$emit('log.info', {
              'msg': 'Delingskonfiguration af kanal lykkedes.',
              'timeout': 3000
            });
          },
          function(reason) {
            busService.$emit('log.error', {
              'cause': reason,
              'msg': 'Deling af kanal fejlede.'
            });
           }
        );
      };
    }
  }
]);
