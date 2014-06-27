ikApp.controller('TemplatesController', function($scope, templateFactory) {
  $scope.templates = templateFactory.getTemplates();
});
