/**
 * @file
 * Images controller handles the media overview page.
 */
ikApp.controller('MediaOverviewController', function ($scope, $location, $sce) {
  $scope.$on('mediaOverview.selectImage', function(event, image) {
    $location.path('/media/' + image.id);
  });

  // Mouse hover on image.
  $scope.hovering = false;


  /**
   * Adds hover overlay on media elements.
   */
  $scope.mouseHover = function(state) {
    if(state > 0) {
      $scope.hovering = state;
    } else {
      $scope.hovering = false;
    }
  }


  /**
   * Sets the correct local path to the video
   */
  $scope.videoURL = function(element) {
    var filepath = '/uploads/media/default/0001/01/' + element.provider_reference;
    return filepath;
  }


  /**
   * Controls play / pause states for video.
   */
  $scope.playPause = function(element) {
    // Fetch the right video.
    var video=document.getElementById("video-" + element.id);
    // Run function on video end.
    document.getElementById("video-" + element.id).addEventListener('ended',isEnded,false);

    // Play / Pause the video.
    if (video.paused) {
      video.play();
      $scope.paused = false;
    }
    else {
      video.pause();
      $scope.paused = true;
    }

    // Set variable used for state class.
    function isEnded(e) {
      if(!e) { e = window.event; }
      $scope.paused = true;
    }
  }
});
