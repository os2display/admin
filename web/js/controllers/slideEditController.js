/**
 * Slide edit controller. Controls the slide creation process.
 */
ikApp.controller('SlideEditController', function($scope, $sce, $http, mediaFactory, slideFactory) {
  // Get the slide from the backend.
  slideFactory.getEditSlide(null).then(function(data) {
    $scope.slide = data;
  });

  $scope.step = 'background-picker';

  // Setup editor states and functions.
  $scope.editor = {
    showTextEditor: false,
    showBackgroundEditor: false,
    showVideoEditor: false,
    toggleTextEditor: function() {
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showVideEditor = false;
      $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
    },

    toggleBackgroundEditor: function() {
      $scope.step = 'background-picker';
      $scope.editor.showTextEditor = false;
      $scope.editor.showVideEditor = false;
      $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
    },

    toggleVideoEditor: function() {
      $scope.editor.showTextEditor = false;
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showVideoEditor = !$scope.editor.showVideoEditor;
    }
  }

  $scope.setStep = function setStep(step) {
    $scope.step = step;
  }

  $scope.backgroundPicker = function backgroundPicker() {
    $scope.step = 'background-picker';
  };

  $scope.pickFromMedia = function pickFromMedia() {
    $scope.step = 'pick-from-media';
  };

  $scope.pickFromComputer = function pickFromComputer() {
    $scope.step = 'pick-from-computer';
  };

  $scope.clickBackgroundColor = function clickBackgroundColor() {
    $scope.slide.options.images = [];
  }

  // Register event listener for select media.
  $scope.$on('mediaOverview.selectMedia', function(event, media) {
    // Handle selection of video or image.
    if (media.content_type.indexOf('image/') === 0) {
      var index = $scope.slide.options.images.indexOf(media.id);

      if (index > -1) {
        $scope.slide.options.images.splice(index, 1);
        $scope.slide.currentImage = '';
      }
      else {
        $scope.slide.options.images = [];
        $scope.slide.options.images.push(media.id);
        $scope.slide.imageUrls[media.id] = media.urls;
      }
    }
    else if (media.content_type.indexOf('video/') === 0) {
      var index = $scope.slide.options.videos.indexOf(media.id);

      if (index > -1) {
        $scope.slide.options.videos.splice(index, 1);
      }
      else {
        $scope.slide.options.videos.push(media.id);
      }
    }

    $scope.step = 'background-picker';
    $scope.editor.showBackgroundEditor = false;
    $scope.editor.showTextEditor = false;
  });

  // Register event listener for media upload success.
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
        $scope.slide.options.images = [];
        $scope.slide.options.images.push(image.id);
        $scope.slide.imageUrls[image.id] = image.urls;
      });

      $scope.step = 'background-picker';
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showTextEditor = false;
    }
  });
});