/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupController', ['busService', '$scope', '$timeout', 'ModalService', '$routeParams',
  function (busService, $scope, $timeout, ModalService, $routeParams) {
    'use strict';

    $scope.loading = true;
    $scope.group = null;

    /**
     * Save group.
     */
    $scope.saveGroup = function () {
      $scope.loading = true;

      busService.$emit('groupService.updateGroup', $scope.group);
    };

    /**
     * returnGroups listener.
     * @type {*}
     */
    var cleanupGroupListener = busService.$on('groupService.returnGroup', function (event, group) {
      $timeout(function () {
        $scope.group = group;

        $scope.loading = false;
      });
    });

    busService.$emit('groupService.getGroup', { id: $routeParams.id });

    /**
     * returnUpdateErrorGroup listener.
     * @type {*}
     */
    var cleanupReturnUpdateGroupErrorListener = busService.$on('groupService.returnUpdateGroupError', function (event, err) {
      $timeout(function () {
        $scope.loading = false;

        // @TODO: Handle error.
        console.log(err);
      });
    });
    
    /**
     * returnUpdateGroup listener.
     * @type {*}
     */
    var cleanupReturnUpdateGroupListener = busService.$on('groupService.returnUpdateGroup', function (event, group) {
      $timeout(function () {
        $scope.loading = false;
    
        // Display message success.
        busService.$emit('log.info', {
          timeout: 3000,
          msg: 'Brugeren opdateret.'
        });
      });
    });

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function destroy() {
      cleanupGroupListener();
      cleanupReturnUpdateGroupErrorListener();
      cleanupReturnUpdateGroupListener();
    });
  }
]);
