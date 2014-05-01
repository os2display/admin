function getParameterByName(name) {
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function SlideCtrl($scope) {
  $scope.init = function() {
    var id = getParameterByName('id');

    if (id !== "") {
      $.get("backend/backend.php", {id: id, req: "loadslide"})
        .done(function(data) {
          console.log(data);
          $scope.$apply(function() {
            $scope.slide = JSON.parse(data);
          });
        });
    }
  };
  $scope.slide = {
    title: '',
    textColor: '#fff',
    textBackgroundColor: '#000',
    backgroundColor: '#ddd',
    backgroundImage: '',
    text: '',
    id: ''
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
      $scope.editor.showBackgroundEditor = false;
    },
    toggleShowBackgroundEditor: function() {
      $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
      $scope.editor.showTextEditor = false;
    },
    saveSlide: function() {
      $.post("backend/backend.php?req=saveslide", {
        title: $scope.slide.title,
        text: $scope.slide.text,
        textColor: $scope.slide.textColor,
        textBackgroundColor: $scope.slide.textBackgroundColor,
        backgroundColor: $scope.slide.backgroundColor,
        backgroundImage: $scope.slide.backgroundImage,
        id: $scope.slide.id
      })
        .done(function(data) {
          window.location.href = "index.html";
        });
    }
  };
}