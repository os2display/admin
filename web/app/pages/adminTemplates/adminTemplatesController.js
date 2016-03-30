/**
 * @file
 * Contains the admin sharing controller.
 */

/**
 * Admin sharing controller.
 */
angular.module('ikApp').controller('AdminTemplatesController', ['busService', 'templateFactory', '$scope',
  function (busService, templateFactory, $scope) {
    'use strict';

    $scope.saving = false;
    $scope.screenTemplates = [];
    $scope.slideTemplates = [];
    $scope.enabledScreenTemplates = [];
    $scope.enabledSlideTemplates = [];

    templateFactory.getAllScreenTemplates().then(
      function success(data) {
        $scope.screenTemplates = data;

        var arr = [];
        for (var i = 0; i < data.length; i++) {
          if (data[i].enabled) {
            arr.push(data[i]);
          }
        }
        $scope.enabledScreenTemplates = arr;
      },
      function error(reason) {
        itkLog.error('Hentning af tilgængelige templates fejlede.', reason);
      }
    );
    templateFactory.getAllSlideTemplates().then(
      function success(data) {
        $scope.slideTemplates = data;

        var arr = [];
        for (var i = 0; i < data.length; i++) {
          if (data[i].enabled) {
            arr.push(data[i]);
          }
        }
        $scope.enabledSlideTemplates = arr;
      },
      function error(reason) {
        itkLog.error('Hentning af tilgængelige templates fejlede.', reason);
      }
    );

    $scope.save = function () {
      $scope.saving = true;
      templateFactory.saveEnabledTemplates($scope.enabledScreenTemplates, $scope.enabledSlideTemplates).then(
        function success() {
          itkLog.info('Template valg gemt', 3000);
          $scope.saving = false;
        },
        function error(reason) {
          itkLog.error('Template valg blev ikke gemt', reason);
          $scope.saving = false;
        }
      );
    }
  }
]);