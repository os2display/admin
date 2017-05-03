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

    busService.$on('groupService.getGroups', function requestGroup(event, args) {
      $http.get('/api/group')
      .success(function (data) {
        busService.$emit('groupService.returnGroups', data);
      })
      .error(function () {
        busService.$emit('groupService.returnGroupsError', err);
      });
    });

    /**
     * Get group event listener.
     */
    busService.$on('groupService.getGroup', function requestGroup(event, args) {
      $http.get('/api/group/' + args.id)
      .success(function (data) {
        busService.$emit('groupService.returnGroup', data);
      })
      .error(function (err) {
        busService.$emit('groupService.returnGroupError', err);
      });
    });
    
    /**
     * Update group event listener.
     */
    busService.$on('groupService.updateGroup', function requestGroup(event, args) {
      $http.put('/api/group/' + args.id, args)
      .success(function (data) {
        busService.$emit('groupService.returnUpdateGroup', data);
      })
      .error(function (err) {
        busService.$emit('groupService.returnUpdateGroupError', err);
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
