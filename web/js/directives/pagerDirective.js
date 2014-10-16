/**
 * @file
 *
 */
ikApp.directive('ikPager', function() {
  "use strict";
  return {
    restrict: 'E',
    replace: true,
    scope: true,
    controller: function($scope) {

      /**
       * Click handler to change page.
       *
       * @param page
       */
      $scope.changePage = function changePage(page) {
        $scope.pager.page = page;
        $scope.updateSearch();
      };

      // Keep an any on changes in number of hits.
      $scope.$watch('hits', function(hits) {
          if (hits > $scope.pager.size) {
            var ceil = Math.ceil(hits / $scope.pager.size);
            var pages = [];
            for (var i=0; i < ceil; i++) {
              pages.push(i);
            }
          }
          $scope.pager.pages = pages;
        }
      );
    },
    templateUrl: '/partials/directives/pager.html'
  };
});

