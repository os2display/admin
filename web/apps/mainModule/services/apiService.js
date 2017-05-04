/**
 * Entity service.
 */
angular.module('mainModule').service('apiService', ['busService', '$http',
  function (busService, $http) {
    'use strict';

    /**
     * Get entities event listener.
     */
    busService.$on('entityService.getEntities', function requestEntity(event, args) {
      $http.get('/api/entity')
      .success(function (data) {
        busService.$emit('entityService.returnEntities', data);
      })
      .error(function (err) {
        busService.$emit('entityService.returnEntities', data);
      });
    });

    /**
     * Get entity event listener.
     */
    busService.$on('entityService.getEntity', function requestEntity(event, args) {
      $http.get('/api/entity/' + args.id)
      .success(function (data) {
        busService.$emit('entityService.returnEntity', data);
      })
      .error(function (err) {
        busService.$emit('entityService.returnEntityError', err);
      });
    });

    /**
     * Get current entity event listener.
     */
    busService.$on('entityService.getCurrentEntity', function requestEntity(event, args) {
      $http.get('/api/entity/current')
        .success(function (data) {
          busService.$emit('entityService.returnCurrentEntity', data);
        })
        .error(function (response) {
          busService.$emit('log.error', {
            'cause': response,
            'msg': 'Bruger kunne ikke hentes'
          });
        });
    });

    /**
     * Create entity event listener.
     */
    busService.$on('entityService.createEntity', function requestEntity(event, args) {
      $http.post('/api/entity', args)
      .success(function (data) {
        busService.$emit('entityService.returnCreateEntity', data);
      })
      .error(function (err) {
        busService.$emit('entityService.returnCreateEntityError', err);
      });
    });

    /**
     * Update entity event listener.
     */
    busService.$on('entityService.updateEntity', function requestEntity(event, args) {
      $http.put('/api/entity/' + args.id, args)
      .success(function (data) {
        busService.$emit('entityService.returnUpdateEntity', data);
      })
      .error(function (err) {
        busService.$emit('entityService.returnUpdateEntityError', err);
      });
    });

    /**
     * Delete entity event listener.
     */
    busService.$on('entityService.deleteEntity', function requestEntity(event, args) {
      $http.delete('/api/entity/' + args.id)
      .success(function (data) {
        busService.$emit('entityService.returnDeleteEntity', data);
      })
      .error(function (err) {
        busService.$emit('entityService.returnDeleteEntityError', err);
      });
    });
  }
]);
