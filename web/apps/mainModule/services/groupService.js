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
  }
]);
