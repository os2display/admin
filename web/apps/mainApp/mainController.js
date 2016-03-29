/**
 * @file
 */

angular.module('mainApp').controller('mainController', ['busService', '$scope',
  function (busService, $scope) {
    'use strict';

    busService.$emit('mainAppReady', { 'status': 'loaded' });
  }
]);
