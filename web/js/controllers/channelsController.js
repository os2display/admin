ikApp.controller('ChannelsController', function($scope, channelFactory) {
  $scope.channels = [];

  channelFactory.getChannels().then(function(data) {
    $scope.channels = data;
  });

  $scope.sort = '-created';
  $scope.search = {
    title: '',
    orientation: 'landscape'
  }

  $scope.setOrientation = function(orientation) {
    $scope.search.orientation = orientation;
  };

  $scope.setSort = function(sort) {
    $scope.sort = sort;
  };
});