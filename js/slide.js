function SlideCtrl($scope) {
  $scope.slide = {
    title: '',
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
    },
    saveSlide: function() {
      $.post("backend.php?req=save", {
        title: $scope.slide.title,
        text: $scope.slide.text,
        textcolor: $scope.slide.textColor,
        textbgcolor: $scope.slide.textBackgroundColor,
        bgcolor: $scope.slide.backgroundColor,
        bgimage: $scope.slide.backgroundImage
      })
      .done(function( data ) {
        alert("Slide gemt.");
      });
    }
  };
  $scope.saveSlide = function() {
    alert("fisk");
  }
}