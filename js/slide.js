function getParameterByName( name ) //courtesy Artem
{
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
    var title = getParameterByName('title');

    if (title !== "") {
      $.get("backend.php", {title: title, req: "load"})
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
        textColor: $scope.slide.textColor,
        textBackgroundColor: $scope.slide.textBackgroundColor,
        backgroundColor: $scope.slide.backgroundColor,
        backgroundImage: $scope.slide.backgroundImage
      })
        .done(function(data) {
          alert("Slide gemt.");
        });
    }
  };
}