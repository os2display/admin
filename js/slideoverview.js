function SlideOverviewCtrl($scope) {
  $scope.slides = [];
  $scope.init = function() {
    $.get("backend.php?req=loadall")
      .done(function(data) {
        $scope.$apply(function() {
          $scope.slides = JSON.parse(data);
        });
      });
  };
}