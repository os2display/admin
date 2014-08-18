/**
 * Slide edit controller. Controls the slide creation process.
 */
ikApp.controller('SlideEditController', function($scope, $http, mediaFactory, slideFactory) {
  // Get the slide from the backend.
  slideFactory.getEditSlide(null).then(function(data) {
    $scope.slide = data;
  });

  $scope.step = 'background-picker';

  // Setup editor states and functions.
  $scope.editor = {
    showTextEditor: false,
    toggleTextEditor: function() {
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
    },
    showBackgroundEditor: false,
    toggleBackgroundEditor: function() {
      $scope.step = 'background-picker';
      $scope.editor.showTextEditor = false;
      $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
    }
  }

  $scope.pickFromMedia = function pickFromMedia() {
    $scope.step = 'pick-from-media';
  };

  $scope.pickFromComputer = function pickFromComputer() {
    $scope.step = 'pick-from-computer';
  };

  $scope.$on('mediaOverview.selectImage', function(event, image) {
    if ($scope.slide.options.image === image.url) {
      $scope.slide.options.image = '';
    }
    else {
      $scope.slide.options.image = image.url;
    }

    $scope.step = 'background-picker';
    $scope.editor.showBackgroundEditor = false;
    $scope.editor.showTextEditor = false;
  });

  $scope.$on('mediaUpload.uploadSuccess', function(event, data) {
    var allSuccess = true;

    for (var i = 0; i < data.queue.length; i++) {
      var item = data.queue[i];

      if (!item.isSuccess) {
        allSuccess = false;
        break;
      }
    }

    if (allSuccess) {
      mediaFactory.getImage(data.id).then(function(image) {
        console.log(data.id);
        console.log(image);

        $scope.slide.options.image = image.urls.landscape;
      });

      $scope.step = 'background-picker';
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showTextEditor = false;
    }
  });
});