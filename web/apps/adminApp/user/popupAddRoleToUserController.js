/**
 * @file
 * @TODO
 */

angular.module('adminApp').controller('PopupAddRoleToUser', [
  'busService', '$scope', '$timeout', '$controller', 'close', 'options',
  function (busService, $scope, $timeout, $controller, close, options) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', { $scope: $scope });

    $scope.loading = false;
    $scope.clickCallback = options.clickCallback;
    $scope.heading = options.heading;
    $scope.searchPlaceholder = options.searchPlaceholder;

    function addElement(element) {
      var f = options.list.find(function (el) {
        return el.id === element.id;
      });

      if (!f) {
        $scope.elements.push({
          id: element.id,
          title: element.title,
          entity: element,
          click: $scope.clickCallback
        });
      }
    }

    $scope.elements = [];
    $scope.getEntities(options.type).then(
      function success(res) {
        for (var el in res) {
          addElement({id: el, title: res[el]});
        }
      },
      function error(err) {
        busService.$emit('log.error', {
          cause: err.code,
          msg: 'Roller kunne ikke hentes.'
        });
      }
    ).then(function () {
      $scope.loading = false;
    });

    /**
     * Close the modal.
     */
    $scope.closeModal = function () {
      close(null);
    };
  }
]);
