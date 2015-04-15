/**
 * @file
 * Contains the channel factory.
 */

/**
 * Koba factory.
 */
angular.module('ikApp').factory('kobaFactory', ['$http', '$q',
  function ($http, $q) {
    'use strict';

    var factory = {};

    factory.getResources = function getResources() {
      var defer = $q.defer();

      $http.get('/api/resources')
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    factory.getBookingsForResource = function getBookingsForResource(resourceMail, from, to) {
      var defer = $q.defer();

      $http.get('/api/resources/' + resourceMail + '/from/' + from + '/to/' + to)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    return factory;
  }
]);
