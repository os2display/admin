/**
 * Api service.
 */
angular.module('mainModule').service('apiService', [
  'busService', '$http',
  function (busService, $http) {
    'use strict';

    function sendRequest(method, url, returnEvent, data) {
      $http({
        method: method,
        url: url,
        data: data
      }).then(
        function success(response) {
          busService.$emit(returnEvent, response.data);
        },
        function error(response) {
          busService.$emit(returnEvent, response.data);
        });
    }

    busService.$on('apiService.request', function request(events, args) {
      sendRequest(args.method, args.url, args.returnEvent, args.data);
    });

    busService.$on('apiService.getEntities', function getEntities(event, args) {
      sendRequest('get', '/api/' + args.type, args.returnEvent);
    });

    busService.$on('apiService.getEntity', function getEntity(event, args) {
      sendRequest('get', '/api/' + args.type + '/' + args.data.id, args.returnEvent);
    });

    busService.$on('apiService.createEntity', function createEntity(event, args) {
      sendRequest('post', '/api/' + args.type, args.returnEvent, args.data);
    });

    busService.$on('apiService.updateEntity', function updateEntity(event, args) {
      sendRequest('put', '/api/' + args.type + '/' + args.data.id, args.returnEvent, args.data);
    });

    busService.$on('apiService.deleteEntity', function deleteEntity(event, args) {
      sendRequest('delete', '/api/' + args.type + '/' + args.data.id, args.returnEvent, args.data);
    });
  }
]);
