angular.module('styleguideComponentsApp').directive('popup', [
  function () {
    return {
      restrict: 'E',
      scope: {
        open: '='
      },
      replace: true,
      transclude: true,
      template:
        '<div class="popup" ng-class="{\'is-visible\': open }">' +
          '<div class="popup--dialog" ng-class="{\'is-visible\': open }">' +
            '<div class="popup--content" ng-transclude>' +
            '</div>' +
          '</div>' +
        '</div>'
    };
  }
]);