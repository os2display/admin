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
    text: '',
    background: '',
    showTextEditor: function() {
      $scope.editor.text = 'is-visible';
    },
    hideTextEditor: function() {
      $scope.editor.text = '';
    },
    showBackgroundEditor: function() {
      $scope.editor.background = 'is-visible';
    },
    hideBackgroundEditor: function() {
      $scope.editor.background = '';
    },
    removeAll: function() {
      $scope.editor.text = '';
      $scope.editor.background = '';
    }
  }
}