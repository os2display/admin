/**
 * @file
 * Paging directive.
 */

/**
 * Paging directive.
 */
angular.module('ikApp').directive('ikPager', [
  function () {
    'use strict';

    return {
      restrict: 'E',
      replace: true,
      scope: true,
      controller: function ($scope) {
        // The lowest page to show.
        $scope.pagesFrom = 0;
        // The max number of results to show.
        $scope.pagesPerLine = 10;

        /**
         * Click handler to change page.
         *
         * @param page
         */
        $scope.changePage = function changePage(page) {
          $scope.pager.page = page;
          $scope.updateSearch();

          $scope.pagesFrom = Math.floor($scope.pager.page / $scope.pagesPerLine) * $scope.pagesPerLine;
        };

        /**
         * Move one page back.
         */
        $scope.prevPage = function prevPage() {
          if ($scope.pager.page > 0) {
            $scope.pager.page--;
            $scope.updateSearch();
          }

          if ($scope.pager.page < $scope.pagesFrom) {
            $scope.pagesFrom = $scope.pagesFrom - $scope.pagesPerLine;
          }
        };

        /**
         * Move one page forward.
         */
        $scope.nextPage = function nextPage() {
          if ($scope.pager.page < $scope.pager.max - 1) {
            $scope.pager.page++;
            $scope.updateSearch();
          }

          if ($scope.pager.page === $scope.pagesFrom + $scope.pagesPerLine) {
            $scope.pagesFrom = $scope.pagesFrom + $scope.pagesPerLine;
          }
        };

        /**
         * Move $scope.pagesPerLine results back.
         */
        $scope.prevLine = function prevLine() {
          if ($scope.pager.page > 0) {
            $scope.pager.page = $scope.pager.page - $scope.pagesPerLine;
            if ($scope.pager.page < 0) {
              $scope.pager.page = 0;
            }
            $scope.updateSearch();
          }

          $scope.pagesFrom = Math.floor($scope.pager.page / $scope.pagesPerLine) * $scope.pagesPerLine;
        };

        /**
         * Move $scope.pagesPerLine results forward.
         */
        $scope.nextLine = function nextLine() {
          if ($scope.pager.page < $scope.pager.max - 1) {
            $scope.pager.page = $scope.pager.page + $scope.pagesPerLine;
            if ($scope.pager.page >= $scope.pager.max) {
              $scope.pager.page = $scope.pager.max - 1;
            }
            $scope.updateSearch();
          }

          $scope.pagesFrom = Math.floor($scope.pager.page / $scope.pagesPerLine) * $scope.pagesPerLine;
        };

        // Keep a watch on changes in number of hits.
        $scope.$watch('hits', function watchHits(hits) {
            var pages = [];
            $scope.pager.max = 0;
            if (hits > $scope.pager.size) {
              $scope.pager.max = Math.ceil(hits / $scope.pager.size);
              for (var i = 0; i < $scope.pager.max; i++) {
                pages.push(i);
              }
            }
            $scope.pager.pages = pages;
          }
        );
      },
      templateUrl: '/apps/ikApp/shared/elements/pager/pager-directive.html?' + window.config.version
    };
  }
]);

