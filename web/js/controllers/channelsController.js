ikApp.controller('ChannelsController', function($scope, channelFactory) {
  $scope.channels = [];
  $scope.search = {
    fields: 'title',
    text: '',
    orientation: 'landscape',
    sort: {
      field: 'created',
      order: 'desc'
    }
  }

  channelFactory.searchLatestChannels().then(
    function(data) {
      $scope.channels = data;
    }
  );

  var updateChannels = function() {
    channelFactory.searchChannels($scope.search).then(
      function(data) {
        $scope.channels = data;
      }
    );
  }

  $scope.setOrientation = function(orientation) {
    $scope.search.orientation = orientation;

    updateChannels();
  };

  $scope.setSort = function(sort, sortOrder) {
    if (sort === $scope.search.sort.field && sortOrder === $scope.search.sort.order) {
      return;
    }

    $scope.search.sort.field = sort;
    $scope.search.sort.order = sortOrder;

    updateChannels();
  };

  $('.js-text-field').off("keyup").on("keyup", function() {
    updateChannels();
  });
});
