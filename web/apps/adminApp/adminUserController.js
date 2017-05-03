/**
 * @file
 * Controller for the admin users page.
 */

angular.module('adminApp').controller('AdminUserController', ['busService', '$scope', '$timeout', 'ModalService', '$routeParams',
  function (busService, $scope, $timeout, ModalService, $routeParams) {
    'use strict';

    $scope.loading = true;
    $scope.user = null;

    /**
     * returnUsers listener.
     * @type {*}
     */
    var cleanupUserListener = busService.$on('userService.returnUser', function (event, user) {
      $timeout(function () {
        $scope.user = user;

        $scope.loading = false;
      });
    });

    busService.$emit('userService.getUser', { id: $routeParams.id });

    /**
     * Save user.
     */
    $scope.saveUser = function () {
      $scope.loading = true;

      busService.$emit('userService.updateUser', $scope.user);
    };

    /**
     * returnUpdateErrorUser listener.
     * @type {*}
     */
    var cleanupReturnUpdateUserErrorListener = busService.$on('userService.returnUpdateUserError', function (event, err) {
      $timeout(function () {
        $scope.loading = false;

        // @TODO: Handle error.
        console.log(err);
      });
    });
    
    /**
     * returnUpdateUser listener.
     * @type {*}
     */
    var cleanupReturnUpdateUserListener = busService.$on('userService.returnUpdateUser', function (event, user) {
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
      cleanupUserListener();
      cleanupReturnUpdateUserErrorListener();
      cleanupReturnUpdateUserListener();
    });
  }
]);
