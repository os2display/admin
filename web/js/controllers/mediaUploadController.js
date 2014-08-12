/**
 * @file
 * Images controller handles the display, selection and upload of image.
 */
ikApp.controller('MediaUploadController', function ($scope, $fileUploader, $location) {
  $scope.currentStep = 1;

  // Create an uploader
  var uploader = $scope.uploader = $fileUploader.create({
    scope: $scope,
    url: '/api/media'
  });

  /**
   * @TODO: comment missing
   */
  $scope.selectFiles = function() {
    angular.element( document.querySelector( '#select-files' )).click();
  };

  /**
   * @TODO: comment missing
   */
  $scope.isImage = function(item) {
    var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
    return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
  };

  /**
   * @TODO: comment missing
   * // Images only
   */
  uploader.filters.push(function(item /*{File|HTMLInputElement}*/) {
    var type = uploader.isHTML5 ? item.type : '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
    type = '|' + type.toLowerCase().slice(type.lastIndexOf('/') + 1) + '|';
    return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
  });

  uploader.bind('afteraddingfile', function (event, item) {
    item.formData.push = item.file.name;
  });

  uploader.bind('afteraddingall', function (event, items) {
    $scope.currentStep++;
  });

  uploader.bind('completeall', function (event, items) {
    $location.path('/media');
    $scope.$apply();
  });
});
