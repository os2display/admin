/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupsController', [
  'busService', '$scope', '$timeout', 'ModalService', '$controller',
  function (busService, $scope, $timeout, ModalService, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', {$scope: $scope});

    $scope.groupsLoading = true;
    $scope.groups = null;
    $scope.max = 50;

    /**
     * Add a group to the groups array.
     *
     * @param group
     *   The group to add.
     */
    function addGroup(group) {
      var actions = [];

      if ($scope.canRead(group)) {
        actions.push({
          url: '#/admin/group/' + group.id,
          title: 'Se gruppe'
        });
      }
      if ($scope.canUpdate(group)) {
        actions.push({
          url: '#/admin/group/' + group.id,
          title: 'Rediger gruppe'
        });
      }
      if ($scope.canDelete(group)) {
        actions.push({
          click: $scope.deleteGroup,
          entity: group,
          title: 'Slet gruppe'
        });
      }

      // Add group.
      $scope.groups.push({
        id: group.id,
        url: '#/admin/group/' + group.id,
        title: group.title,
        actions: actions
      });
    }

    /**
     * Remove a group to the groups array.
     *
     * @param group
     *   The group to remove.
     */
    function removeGroup(group) {
      $timeout(function () {
        var findGroup = $scope.groups.findIndex(function (element, index, array) {
          return element.id === group.id;
        });

        if (findGroup) {
          $scope.groups.splice(findGroup, 1);
        }
      });
    }

    /**
     * returnGroups listener.
     * @type {*}
     */
    var cleanupGetGroupsListener = busService.$on('AdminGroupsController.returnGroups', function (event, result) {
      $timeout(function () {
        if (result.error) {
          // Display message success.
          busService.$emit('log.error', {
            cause: result.error.code,
            msg: 'Grupper kunne ikke hentes.'
          });

          // Remove spinner.
          $scope.groupsLoading = false;

          return;
        }

        $scope.groups = [];

        for (var group in result) {
          if (result.hasOwnProperty(group)) {
            addGroup(result[group]);
          }
        }

        $scope.groupsLoading = false;
      });
    });

    // Get groups.
    busService.$emit('apiService.getEntities', {
      type: 'group',
      returnEvent: 'AdminGroupsController.returnGroups'
    });

    /**
     * Show create group modal.
     */
    $scope.createGroup = function () {
      // Show modal.
      ModalService.showModal({
        templateUrl: "apps/adminApp/group/popup-create-group.html",
        controller: "PopupCreateGroup"
      }).then(function (modal) {
        modal.close.then(function (group) {
          if (group) {
            addGroup(group);
          }
        });
      });
    };

    /**
     * Show delete group modal.
     */
    $scope.deleteGroup = function (group) {
      // Show modal.
      ModalService.showModal({
        templateUrl: "apps/adminApp/group/popup-delete-group.html",
        controller: "PopupDeleteGroup",
        inputs: {
          group: group
        }
      }).then(function (modal) {
        modal.close.then(function (group) {
          if (group) {
            removeGroup(group);
          }
        });
      });
    };

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function destroy() {
      cleanupGetGroupsListener();
    });
  }
]);
