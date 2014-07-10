ikApp.controller('ChannelsController', function($scope, channelFactory) {
  $scope.channels = [];
  $scope.search = {
    fields: 'title',
    text: '',
  };

  $scope.search.filter = {};
  $scope.search.filter['orientation'] = 'landscape';

  $scope.search.sort = {};
  $scope.search.sort['created'] = 'desc';

  channelFactory.searchLatestChannels().then(
    function(data) {
      $scope.channels = data;
    }
  );

  var updateChannels = function() {
    channelFactory.searchChannels($scope.search).then(
      function(data) {
        console.log(data);
        $scope.channels = data;
      }
    );
  };

  $scope.setOrientation = function(orientation) {
    $scope.search.filter['orientation'] = orientation;

    updateChannels();
  };

  $scope.setSort = function(sort, sortOrder) {
    $scope.search.sort = {};
    $scope.search.sort[sort] = sortOrder;

    updateChannels();
  };

  $('.js-text-field').off("keyup").on("keyup", function() {
    updateChannels();
  });
});
