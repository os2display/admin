/**
 * @file
 * Contains directives for media upload.
 */

/**
 * Media Upload Directive.
 *
 * Emits the mediaUpload.uploadSuccess event when upload i successful.
 *
 * Emits the 'mediaUpload.uploadComplete' event for a parent controller to catch.
 *   Catch this event to handle when the upload is complete.
 */
angular.module('ikApp').directive('ikMediaUpload', [
  function () {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikUploadType: '@',
        queueLimit: '='
      },
      controller: function ($scope, FileUploader) {
        $scope.currentStep = 1;
        $scope.uploadComplete = false;
        $scope.uploadErrors = false;
        $scope.uploadInProgress = false;
        $scope.uploadErrorText = '';

        var acceptedVideotypes = '|mp4|x-msvideo|x-ms-wmv|quicktime|mpeg|mpg|x-matroska|ogg|webm';
        var acceptedImagetypes = '|jpg|png|jpeg|bmp|gif';

        // Set accepted media types.
        var acceptedMediatypes = '';
        if ($scope.ikUploadType === 'image' || $scope.ikUploadType === 'logo') {
          acceptedMediatypes = acceptedImagetypes;
        } else if ($scope.ikUploadType === 'video') {
          acceptedMediatypes = acceptedVideotypes;
        } else {
          acceptedMediatypes = acceptedVideotypes + acceptedImagetypes;
        }

        // Create an uploader
        $scope.uploader = new FileUploader({
          url: '/api/media',
          queueLimit: $scope.queueLimit ? $scope.queueLimit : 1,
          filters: [{
            name: 'mediaFilter',
            fn: function (item /*{File|FileLikeObject}*/) {
              var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1);
              return acceptedMediatypes.indexOf(type) !== -1;
            }
          }]
        });

        /**
         * Calls the hidden select files button.
         */
        $scope.selectFiles = function () {
          angular.element(document.querySelector('#select-files')).click();
        };

        /**
         * Clear the uploader queue.
         */
        $scope.clearQueue = function () {
          $scope.uploader.clearQueue();
          $scope.uploadComplete = false;
          $scope.uploadErrors = false;
          $scope.currentStep = 1;
          $scope.uploadErrorText = '';
        };

        /**
         * Remove item from the uploader queue.
         * @param item
         */
        $scope.removeItem = function (item) {
          item.remove();
          if ($scope.uploader.queue.length <= 0) {
            $scope.currentStep = 1;
            $scope.uploadComplete = false;
            $scope.uploadErrors = false;
          }
        };

        $scope.upload = function upload() {
          $scope.uploadInProgress = true;
          $scope.uploader.uploadAll();
        };

        /**
         * Checks whether the item is an image.
         */
        $scope.isImage = function (item) {
          var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1);
          return acceptedImagetypes.indexOf(type) !== -1;
        };

        /**
         * Returns the uploader progress.
         * NB! THIS IS A HACK!
         *
         * @returns {*}
         */
        $scope.getProgress = function () {
          if (!$scope.uploader.progress) {
            return;
          }

          if ($scope.uploadInProgress && $scope.uploader.progress > 5) {
            return $scope.uploader.progress - 5;
          } else {
            return $scope.uploader.progress;
          }
        };

        /**
         * After adding a file to the upload queue, add an empty title to the file item.
         */
        $scope.uploader.onAfterAddingFile = function (item) {
          item.formData = [
            {
              "title": "",
              "logo": false
            }
          ];

          if ($scope.ikUploadType === 'logo') {
            item.formData[0].logo = true;
          }
        };

        /**
         * After adding all files, increase current step.
         */
        $scope.uploader.onAfterAddingAll = function () {
          $scope.currentStep++;
        };

        /**
         * If an error occurs.
         * @param item
         * @param response
         * @param status
         */
        $scope.uploader.onErrorItem = function (item, response, status) {
          $scope.uploadErrors = true;
          $scope.uploadInProgress = false;

          if (status === 413) {
            $scope.uploadErrorText = "Filen var for stor (fejlkode: 413)";
          } else {
            $scope.uploadErrorText = "Der skete en fejl (fejlkode: " + status + ")";
          }
        };

        /**
         * When all uploads are complete.
         */
        $scope.uploader.onCompleteAll = function () {
          $scope.uploadComplete = true;
          $scope.uploadInProgress = false;
        };

        /**
         * When an item has been uploaded successfully.
         * @param item
         * @param response
         */
        $scope.uploader.onSuccessItem = function (item, response) {
          $scope.$emit('mediaUpload.uploadSuccess', {
            image: item,
            id: response[0],
            queue: $scope.uploader.queue
          });
        };
      },
      link: function () {
      },
      templateUrl: '/apps/ikApp/shared/elements/mediaUpload/media-upload-directive.html?' + window.config.version
    };
  }
]);
