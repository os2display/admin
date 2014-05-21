function ChannelCtrl($scope) {
  $scope.init = function() {
  };
  $scope.channel = {
    title: '',
    id: '',
    orientation: '',
    slides: ''
  };
  $scope.changeOrientationHorizontal = function() {
    $scope.channel.orientation = 'horizontal';
    $scope.editor.orientationHorizontalSelected = 'selected';
    $scope.editor.orientationVerticalSelected = '';
  };
  $scope.changeOrientationVertical = function() {
    $scope.channel.orientation = 'vertical';
    $scope.editor.orientationHorizontalSelected = '';
    $scope.editor.orientationVerticalSelected = 'selected';
  };
  $scope.editor = {
    orientationHorizontalSelected: '',
    orientationVerticalSelected: '',
    page: 1
  };
}