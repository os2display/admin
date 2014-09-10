/**
 * @file
 * Contains the screen groups controller. The List.
 */

/**
 * Screen group controller.
 */
ikApp.controller('ScreenGroupsController', ['$scope', 'screenFactory',
  function($scope, screenFactory) {
    screenFactory.getScreenGroups().then(function(data) {
      $scope.screenGroups = data;
    });

    $scope.sort = 'created_at';
    $scope.search = {
      title: ''
    }

    $scope.setSort = function(sort) {
      $scope.sort = sort;
    };
  }
]);
