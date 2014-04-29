function IndexCtrl($scope) {
  $scope.slides = [];
  $scope.createSlide = function() {
    window.location.href = "slide.html";
  };
  $scope.init = function() {
    $.get("backend.php", {req: "loadall"})
    .done(function(data) {
      $scope.$apply(function() {
        $scope.slides = JSON.parse(data);
      });
    });
  };
}