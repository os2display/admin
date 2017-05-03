/**
 * @file
 * Contains the group service.
 */

/**
 * Group service.
 */
angular.module('mainModule').service('groupService', ['busService', '$http',
  function (busService, $http) {
    'use strict';

    busService.$on('groupService.getGroups', function requestUser(event, args) {
      $http.get('/api/group')
      .success(function (data) {
        busService.$emit('groupService.returnGroups', data);
      })
      .error(function () {
        busService.$emit('groupService.returnGroupsError', err);
      });
    });

    /**
     * Create group event listener.
     */
    busService.$on('groupService.createGroup', function requestGroup(event, args) {
      $http.post('/api/group', args)
      .success(function (data) {
        busService.$emit('groupService.returnCreateGroup', data);
      })
      .error(function (err) {
        busService.$emit('groupService.returnCreateGroupError', err);
      });
    });
  }
]);
