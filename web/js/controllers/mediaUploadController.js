/**
 * @file
 * Images controller handles the display, selection and upload of image.
 */
ikApp.controller('MediaUploadController', function ($scope, FileUploader, $location) {
  $scope.currentStep = 1;

  // Create an uploader
  $scope.uploader = new FileUploader({
    url: '/api/media',
    filters: [{
      name: 'imageFilter',
      fn: function(item /*{File|FileLikeObject}*/, options) {
        var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
        return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
      }
    }]
  });

  /**
   * Calls the hidden select files button.
   */
  $scope.selectFiles = function() {
    angular.element( document.querySelector( '#select-files' )).click();
  };

  /**
   * Clear the uploader queue.
   */
  $scope.clearQueue = function() {
    $scope.uploader.clearQueue();
    $scope.currentStep = 1;
  }

  /**
   * Remove item from the uploader queue.
   * @param item
   */
  $scope.removeItem = function(item) {
    item.remove();
    if ($scope.uploader.queue.length <= 0) {
      $scope.currentStep = 1;
    }
  }

  /**
   * Checks whether the item is an image.
   */
  $scope.isImage = function(item) {
    var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
    return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
  };

  $scope.uploader.onAfterAddingFile = function(item) {
    console.log(item);
    item.formData.push = item.file.name;
  };

  $scope.uploader.onAfterAddingAll = function(item) {
    console.log(item);
    $scope.currentStep++;
  };

  $scope.uploader.onCompleteAll = function() {
    $location.path('/media');
    $scope.$apply();
  };

  $scope.uploader.onCancelItem = function(item, response, status, headers) {
    console.log("cancel item");
  };
});
