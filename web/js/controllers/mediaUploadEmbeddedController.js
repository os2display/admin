/**
 * @file
 * Contains the media upload controller.
 */

/**
 * Media Upload Embedded Controller.
 *
 * Emits the 'mediaUpload.uploadComplete' event for a parent controller to catch.
 *   Catch this event to handle when the upload is complete.
 */
ikApp.controller('MediaUploadEmbeddedController', ['$scope', 'FileUploader',
  function($scope, FileUploader) {
    $scope.currentStep = 1;
    $scope.uploadComplete = false;
    $scope.uploadErrors = false;
    $scope.uploadErrorText = '';

    // Create an uploader
    $scope.uploader = new FileUploader({
      url: '/api/media',
      filters: [{
        name: 'imageFilter',
        fn: function(item /*{File|FileLikeObject}*/, options) {
          var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
          return '|jpg|png|jpeg|bmp|gif|mp4|'.indexOf(type) !== -1;
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
      $scope.uploadComplete = false;
      $scope.uploadErrors = false;
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
        $scope.uploadComplete = false;
        $scope.uploadErrors = false;
      }
    }

    /**
     * Checks whether the item is an image.
     */
    $scope.isImage = function(item) {
      var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
      return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
    };

    /**
     * After adding a file to the upload queue, add an empty title to the file item.
     */
    $scope.uploader.onAfterAddingFile = function(item) {
      item.formData = [{title: ''}];
    };

    /**
     * After adding all files, increase current step.
     * @param item
     */
    $scope.uploader.onAfterAddingAll = function(item) {
      $scope.currentStep++;
    };

    /**
     * If an error occurs.
     * @param item
     * @param response
     * @param status
     * @param headers
     */
    $scope.uploader.onErrorItem = function(item, response, status, headers) {
      $scope.uploadErrors = true;

      if (status === 413) {
        $scope.uploadErrorText = "Billedet var for stort (fejlkode: 413)";
      } else {
        $scope.uploadErrorText = "Der skete en fejl (fejlkode: " + status + ")";
      }
    };

    /**
     * When all uploads are complete.
     */
    $scope.uploader.onCompleteAll = function() {
      $scope.uploadComplete = true;
    };

    /**
     * When an item has been uploaded successfully.
     * @param item
     * @param response
     * @param status
     * @param headers
     */
    $scope.uploader.onSuccessItem = function(item, response, status, headers) {
      $scope.$emit('mediaUpload.uploadSuccess', {
        image: item,
        id: response[0],
        queue: $scope.uploader.queue
      });
    };
  }
]);
