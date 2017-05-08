/**
 * @file
 * Base Api controller.
 */

angular.module('adminApp').controller('BaseApiController', [
  'busService', '$scope', '$controller', '$timeout', '$q',
  function (busService, $scope, $controller, $timeout, $q) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', {$scope: $scope});

    var baseApiCleanupListeners = [];

    /**
     * Creates a uuid.
     *
     * @return {string}
     */
    function createUuid() {
      // http://stackoverflow.com/a/2117523
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
      });
    }

    /**
     * Function to get entity.
     *
     * @param type
     * @param data
     */
    $scope.getEntity = function getEntity(type, data) {
      var deferred = $q.defer();
      var uuid = createUuid();

      baseApiCleanupListeners.push(busService.$on('BaseApiController.returnEntity.' + uuid, function (event, result) {
        if (result.error) {
          deferred.reject(result.error);
        }
        else {
          deferred.resolve(result);
        }
      }));

      busService.$emit('apiService.getEntity', {
        type: type,
        returnEvent: 'BaseApiController.returnEntity.' + uuid,
        data: data
      });

      return deferred.promise;
    };

    /**
     * Function to get entities.
     *
     * @param type
     */
    $scope.getEntities = function getEntities(type) {
      var deferred = $q.defer();
      var uuid = createUuid();

      baseApiCleanupListeners.push(busService.$on('BaseApiController.returnEntities.' + uuid, function (event, result) {
        if (result.error) {
          deferred.reject(result.error);
        }
        else {
          deferred.resolve(result);
        }
      }));

      busService.$emit('apiService.getEntities', {
        type: type,
        returnEvent: 'BaseApiController.returnEntities.' + uuid
      });

      return deferred.promise;
    };

    /**
     * Function to update entity.
     *
     * @param type
     * @param data
     */
    $scope.updateEntity = function updateEntity(type, data) {
      var deferred = $q.defer();
      var uuid = createUuid();

      baseApiCleanupListeners.push(busService.$on('BaseApiController.updateEntity.' + uuid, function (event, result) {
        if (result.error) {
          deferred.reject(result.error);
        }
        else {
          deferred.resolve(result);
        }
      }));

      busService.$emit('apiService.updateEntity', {
        type: type,
        returnEvent: 'BaseApiController.updateEntity.' + uuid,
        data: data
      });

      return deferred.promise;
    };

    /**
     * Function to create an entity.
     *
     * @param type
     * @param data
     */
    $scope.createEntity = function createEntity(type, data) {
      var deferred = $q.defer();
      var uuid = createUuid();

      baseApiCleanupListeners.push(busService.$on('BaseApiController.createEntity.' + uuid, function (event, result) {
        if (result.error) {
          deferred.reject(result.error);
        }
        else {
          deferred.resolve(result);
        }
      }));

      busService.$emit('apiService.createEntity', {
        type: type,
        returnEvent: 'BaseApiController.createEntity.' + uuid,
        data: data
      });

      return deferred.promise;
    };

    /**
     * Function to delete an entity.
     *
     * @param type
     * @param data
     */
    $scope.deleteEntity = function deleteEntity(type, data) {
      var deferred = $q.defer();
      var uuid = createUuid();

      baseApiCleanupListeners.push(busService.$on('BaseApiController.deleteEntity.' + uuid, function (event, result) {
        if (result.error) {
          deferred.reject(result.error);
        }
        else {
          deferred.resolve(result);
        }
      }));

      busService.$emit('apiService.deleteEntity', {
        type: type,
        returnEvent: 'BaseApiController.deleteEntity.' + uuid,
        data: data
      });

      return deferred.promise;
    };

    /**
     * Function to execute custom api request.
     *
     * @param method
     * @param url
     * @param data
     */
    $scope.baseApiRequest = function baseApiRequest(method, url, data) {
      var deferred = $q.defer();
      var uuid = createUuid();

      baseApiCleanupListeners.push(busService.$on('BaseApiController.baseApiRequest.' + uuid, function (event, result) {
        if (result.error) {
          deferred.reject(result.error);
        }
        else {
          deferred.resolve(result);
        }
      }));

      busService.$emit('apiService.request', {
        method: method,
        url: url,
        returnEvent: 'BaseApiController.baseApiRequest.' + uuid,
        data: data
      });

      return deferred.promise;
    };

    /**
     * on destroy.
     *
     * Clean up baseApiCleanupListeners.
     */
    $scope.$on('$destroy', function destroy() {
      for (var listener in baseApiCleanupListeners) {
        baseApiCleanupListeners[listener]();
      }
    });
  }
]);
