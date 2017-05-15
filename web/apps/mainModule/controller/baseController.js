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
    $scope.baseCurrentUser = null;

    // Get the current user.
    userService.getCurrentUser().then(
      function (currentUser) {
        $scope.baseCurrentUser = currentUser;
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
    $scope.baseCanRead = function baseCanRead(entity) {
      return hasPermission(entity, 'can_read');
    };

    /**
     * Can the entity be updated?
     *
     * @param entity
     * @return {*}
     */
    $scope.baseCanUpdate = function baseCanUpdate(entity) {
      return hasPermission(entity, 'can_update');
    };

    /**
     * Can the entity be deleted?
     *
     * @param entity
     * @return {*}
     */
    $scope.baseCanDelete = function baseCanDelete(entity) {
      return hasPermission(entity, 'can_delete');
    };

    /**
     * Can the user create an entity of the given type?
     *
     * @param type
     */
    $scope.baseCanCreate = function baseCanCreate(type) {
      return hasPermission($scope.baseCurrentUser, 'can_create_' + type);
    };

    /**
     * Can the user add a type?
     *
     * @param type
     */
    $scope.baseCanAdd = function baseCanAdd(type) {
      return hasPermission($scope.baseCurrentUser, 'can_add_' + type);
    };

    /**
     * Remove an element from a list where field equals.
     *
     * @param list
     * @param element
     * @param field
     * @return {null}
     */
    $scope.baseRemoveElementFromList = function(list, element, field) {
      var i = list.findIndex(function (el) {
        return element[field] === el[field];
      });
      if (i !== undefined) {
        return list.splice(i, 1);
      }

      return null;
    };
  }
]);
