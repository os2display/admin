ikApp.controller('SlidesController', function($scope, slideFactory) {
  $scope.slides = [];
  $scope.search = {
    fields: 'title',
    text: '',
  };

  $scope.search.filter = {};
  $scope.search.filter['orientation'] = 'landscape';

  $scope.search.sort = {};
  $scope.search.sort['created'] = 'desc';

  slideFactory.searchLatestSlides().then(
    function(data) {
      $scope.slides = data;
    }
  );

  var updateSlides = function() {
    slideFactory.searchSlides($scope.search).then(
      function(data) {
        $scope.slides = data;
      }
    );
  };

  $scope.setOrientation = function(orientation) {
    $scope.search.filter['orientation'] = orientation;

    updateSlides();
  };

  $scope.setSort = function(sort, sortOrder) {
    $scope.search.sort = {};
    $scope.search.sort[sort] = sortOrder;

    updateSlides();
  };

  $('.js-text-field').off("keyup").on("keyup", function() {
    updateSlides();
  });
});
