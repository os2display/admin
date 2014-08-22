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
    toggleTextEditor: function() {
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
    },
    showBackgroundEditor: false,
    toggleBackgroundEditor: function() {
      $scope.step = 'background-picker';
      $scope.editor.showTextEditor = false;
      $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
    },
    showTextEditor: false,
    showBackgroundEditor: false,
    showVideoEditor: false,
    toggleVideoEditor: function() {
      $scope.editor.showVideoEditor = !$scope.editor.showVideoEditor;
    }
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

  // Validate youtube URL.
  $scope.youtubeUrlValidate = function youtubeUrlValidate() {
    var inputString = '';

    // Input field not empty.
    if ($scope.slide.options.youtubeUrl) {
      inputString = $scope.slide.options.youtubeUrl;
      // We only want youtube paths.
      $scope.slide.options.isValid = inputString.indexOf("https://www.youtube.com/watch?v=");
      if ($scope.slide.options.isValid == 0) {

        // Fetch youtube id and save it to slide.
        var url = inputString.split("=");
        $scope.slide.options.youtubeId = url[1];

        // The string is valid.
        return true;
      } else {
        // The string is invalid, and reset youtubeID.
        $scope.slide.options.youtubeId = '';

        return false;
      }
    }
  };

  $scope.trustSrc = function(src) {
    if (src) {
      if ($scope.slide.options.isValid == 0) {
        // Alter the youtube url, to reflect an embed code.
        src = src.replace("watch?v=", "embed/");

        // Add parameters. ("?" mark for first parameter)
        // Hide info.
        src = src + "?showinfo=0";

        //Hide controls.
        src = src + "&controls=0";

        $scope.slide.options.embedCode = $sce.trustAsResourceUrl(src);

        return $scope.slide.options.embedCode;
      }
      else {
        // Provide no source for iframe.
        return '';
      }
    }
  }

  $scope.resetImage = function resetImage() {
    $scope.slide.options.image = '';
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