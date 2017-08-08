/**
 * @file
 * Contains slide edit controller.
 */

/**
 * Slide edit controller. Controls the editors for the slide creation process.
 */
angular.module('ikApp').controller('SlideEditController', [
  '$scope', 'slideFactory', 'busService', 'templateFactory', '$compile', '$templateRequest',
  function ($scope, slideFactory, busService, templateFactory, $compile, $templateRequest) {
    'use strict';

    // Get the slide from the backend.
    slideFactory.getEditSlide(null).then(
      function success(data) {

        $scope.slide = data;

        templateFactory.getSlideTemplate(data.template).then(
          function success(data) {
            $scope.template = data;
          },
          function error(reason) {
            busService.$emit('log.error', {
              'cause': reason,
              'msg': 'Kunne ikke loade værktøjer til slidet.'
            });
          }
        );
      },
      function error(reason) {
        busService.$emit('log.error', {
          'cause': reason,
          'msg': 'Kunne ikke hente slide.'
        });
      }
    );

    busService.$emit('bodyService.removeClass', 'is-locked');

    // Setup editor states and functions.
    $scope.editor = {
      editorOpen: false,
      hideEditors: function hideEditors() {
        busService.$emit('bodyService.removeClass', 'is-locked');
        $scope.editor.editorOpen = false;

        var element = document.getElementById('slide-edit-tool');
        angular.element(element).html(
          $compile("")($scope)
        );
      }
    };

    /**
     * Open the selected tool.
     * @param tool
     */
    $scope.openTool = function openTool(tool) {
      busService.$emit('bodyService.toggleClass', 'is-locked');

      $scope.editor.editorOpen = true;

      if (!tool.id) {
        tool.id = 'base-editor';
      }

      var element = document.getElementById('slide-edit-tool');
      var html = '<div><' + tool.id + ' slide="slide" close="editor.hideEditors()" template="' + tool.template + '"></' + tool.id + '></div>';
      angular.element(element).html(
        $compile(html)($scope)
      );
    };
  }
]);
