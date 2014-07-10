/**
 * @file
 * Channels controller handles the display and selection of channels.
 */


ikApp.controller('ChannelsController', function($scope, channelFactory) {
  $scope.channels = [];

  // Setup default search options.
  $scope.search = {
    "fields": 'title',
    "text": '',
    "filter": {
      "orientation":  'landscape'
    },
    "sort": {
      "created" : {
        "order": "desc"
      }
    }
  };

  // Send the default search query.
  channelFactory.searchChannels($scope.search).then(
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
