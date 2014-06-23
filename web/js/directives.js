/**
 * Overrides the contenteditable html5 tag to make the field reflect an ngModel.
 */
ikApp.directive('contenteditable', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ctrl) {
      // view -> model
      element.bind('blur', function() {
        scope.$apply(function() {
          ctrl.$setViewValue(element.html());
        });
      });

      // model -> view
      ctrl.$render = function() {
        element.html(ctrl.$viewValue);
      };

      // load init value.
      ctrl.$render();

      if (element[0].tagName == 'PRE') {
        // Replace enter, to avoid insertion of html tags in the data field.
        element.on('keydown', function(event) {
          if (event.keyCode == 13) {
            event.preventDefault();
            document.execCommand('InsertHTML', false, '\r\n');
          }
        });
      } else {
        element.on('keydown', function(event) {
          if (event.keyCode == 13) {
            event.preventDefault();
          }
        });
      }
    }
  };
});

/**
 * HTML attribute to replace the ng-include div.
 */
ikApp.directive('includeReplace', function () {
  return {
    require: 'ngInclude',
    restrict: 'A',
    link: function (scope, el, attrs) {
      el.replaceWith(el.children());
    }
  };
});

/**
 * Directive to insert a screen.
 */
ikApp.directive('ikScreen', ['screenFactory', function(screenFactory) {
  return {
    templateUrl: 'partials/screen.html',
    restrict: 'E',
    scope: {},
    link: function(scope, element, attrs) {
      scope.ikScreen = screenFactory.getScreen(attrs.ikId);
    }
  }
}]);

/**
 * Directive to insert html for a slide.
 * @param ik-id: the id of the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlide', ['slideFactory', 'templateFactory', function(slideFactory, templateFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      scope.ikSlide = slideFactory.getSlide(scope.ikId);
      scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

      scope.theStyle = {
        width: "" + scope.ikWidth + "px",
        height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
        fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
      }
    },
    template: '<div data-ng-include="" src="templateURL" include-replace></div>'
  }
}]);

/**
 * Directive to insert html for a slide, that is editable.
 * @param ik-id: the id of the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlideEditable', ['slideFactory', 'imageFactory', function(slideFactory, imageFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      scope.ikSlide = slideFactory.getSlide(scope.ikId);
      scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '-edit.html';
      scope.backgroundImages = imageFactory.getImages();
      scope.editor = {
        showTextEditor: false,
        toggleTextEditor: function() {
          scope.editor.showBackgroundEditor = false;
          scope.editor.showTextEditor = !scope.editor.showTextEditor;
        },
        showBackgroundEditor: false,
        toggleBackgroundEditor: function() {
          scope.editor.showTextEditor = false;
          scope.editor.showBackgroundEditor = !scope.editor.showBackgroundEditor;
        }
      }

      scope.theStyle = {
        width: "" + scope.ikWidth + "px",
        height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
        fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
      }

      // Add keyup event listener for fontsize, to make sure the preview updates font size.
      element.find('.js-ik-slide-editor-fontsize-input').on('keyup', function() {
        scope.theStyle.fontsize =  "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px";
        scope.$apply();
      });

      // Cleanup.
      element.on('$destroy', function() {
        $(this).find('.js-ik-slide-editor-fontsize-input').off('keyup');
      });
    },
    templateUrl: '/partials/slide-edit.html'
  }
}]);
