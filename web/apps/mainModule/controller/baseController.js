/**
 * @file
 * Contains base controller.
 */

/**
 * Base controller.
 */
angular.module('mainModule').controller('BaseController', ['$scope', 'userService',
  function ($scope, userService) {
    'use strict';

    var self = this;
    self.user = null;
    $scope.baseCurrentUser = self.user;

    // Get the current user.
    userService.getCurrentUser().then(
      function (currentUser) {
        self.user = currentUser;
      },
      function error(err) {
        console.error(err);
      }
    );

    /**
     * Check if the entity has a given permission.
     *
     * @param entity
     * @param permission
     * @return {*}
     */
    function hasPermission(entity, permission) {
      return entity && entity.api_data && entity.api_data.permissions && entity.api_data.permissions[permission];
    }

    /**
     * Can the entity be read?
     *
     * @param entity
     * @return {*}
     */
    $scope.canRead = function canRead(entity) {
      return hasPermission(entity, 'can_read');
    };

    /**
     * Can the entity be updated?
     *
     * @param entity
     * @return {*}
     */
    $scope.canUpdate = function canUpdate(entity) {
      return hasPermission(entity, 'can_update');
    };

    /**
     * Can the entity be deleted?
     *
     * @param entity
     * @return {*}
     */
    $scope.canDelete = function canDelete(entity) {
      return hasPermission(entity, 'can_delete');
    };

    /**
     * Can the user create an entity of the given type?
     *
     * @param type
     */
    $scope.canCreate = function canCreate(type) {
      return hasPermission(self.user, 'can_create_' + type);
    };
  }
]);
