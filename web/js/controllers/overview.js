ikApp.controller('OverviewController', function($scope, slideFactory) {
  $scope.slides = slideFactory.getSlides();
  $scope.orientation = 'horisontal';

  $scope.setFilter = function($filter) {
    if ($scope.orientation != $filter) {
      $('.overview--filter-orientation .is-active').removeClass('is-active');
      $(event.target).addClass('is-active');

    }
    $scope.orientation = $filter;
  };

  $scope.setSort = function($sort) {
    if ($scope.sort != $sort) {
      $('.overview--sort-links .is-active').removeClass('is-active');
      $(event.target).addClass('is-active');
    }
    $scope.sort = $sort;
  };
});