ikApp.controller('ScreensController', function($scope, screenFactory) {
  $scope.screens = [];
  $scope.search = {
    fields: 'title',
    text: '',
  };

  $scope.search.filter = {};
  $scope.search.filter['orientation'] = 'landscape';

  $scope.search.sort = {};
  $scope.search.sort['created'] = 'desc';

  screenFactory.searchLatestScreens().then(
    function(data) {
      $scope.screens = data;
    }
  );

  var updateScreens = function() {
    slideFactory.searchScreens($scope.search).then(
      function(data) {
        $scope.screens = data;
      }
    );
  };

  $scope.setOrientation = function(orientation) {
    $scope.search.filter['orientation'] = orientation;

    updateScreens();
  };

  $scope.setSort = function(sort, sortOrder) {
    $scope.search.sort = {};
    $scope.search.sort[sort] = sortOrder;

    updateScreens();
  };

  $('.js-text-field').off("keyup").on("keyup", function() {
    updateScreens();
  });
});
