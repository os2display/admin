(function() {
  var app;

  app = angular.module("ngModal", []);

  app.directive('modalDialog', ['$sce', function($sce) {
      return {
        restrict: 'E',
        scope: {
          show: '=',
          onClose: '&?'
        },
        replace: true,
        transclude: true,
        link: function(scope, element, attrs) {
          scope.hideModal = function() {
            return scope.show = false;
          };
          scope.$watch('show', function(newVal, oldVal) {
            if (newVal && !oldVal) {
              document.getElementsByTagName("body")[0].style.overflow = "hidden";
            } else {
              document.getElementsByTagName("body")[0].style.overflow = "";
            }
            if ((!newVal && oldVal) && (scope.onClose != null)) {
              return scope.onClose();
            }
          });
        },
        templateUrl: "app/shared/ngModal/ng-modal.html"
      };
    }
  ]);

}).call(this);
