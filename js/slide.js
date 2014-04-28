function SlideCtrl($scope) {
  $scope.slide = {
    textColor: '#fff',
    textBackgroundColor: '#000',
    backgroundColor: '#ddd',
    backgroundImage: '',
    text: 'Integer legentibus erat a ante historiarum dapibus. Excepteur sint obcaecat cupiditat non proident culpa. Contra legem facit qui id facit quod lex prohibet.'
  };
  $scope.preview = {
    size: "one-quarter"
  };
  $scope.backgroundImages = [
    {title: "-- Ikke valgt --", src: ""},
    {title: "hill", src: "images/hill.jpg"},
    {title: "ocean", src: "images/ocean.jpg"}
  ];
  $scope.editor = {
    showTextEditor: false,
    showBackgroundEditor: false,
    toggleShowTextEditor: function() {
      $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
    },
    toggleShowBackgroundEditor: function() {
      $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
    }
  }
}