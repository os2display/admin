/**
 * @file
 * Contains panel-menu component.
 * See styleguide: modules/panel-menu.
 */

/**
 * html parameters:
 */
angular.module('styleguideComponentsModule').directive('popup', function(){
  return {
    restrict: 'E',
    replace: true,
    transclude: true,
    template:
      '<div class="popup is-visible is-positioned">' +
        '<div class="popup--dialog is-visible is-positioned">' +
          '<div class="popup--content" ng-transclude>' +
          '</div>' +
        '</div>' +
      '</div>'
  }
});