'use strict';

describe('SlideController - ', function(){
  var scope; //we'll use this scope in our tests
  var mockTemplateFactory, mockSlideFactory;
  var slideController;

  beforeEach(module('ikApp'));

  //mock the controller for the same reason and include $rootScope and $controller
  beforeEach(inject(function($rootScope, $controller){
    //create an empty scope
    scope = $rootScope.$new();

    // Setup mock template factory.
    mockTemplateFactory = {
      getTemplates: function () {
        return [{
          id: 'text-top',
          image: '/ik-templates/text-top/text-top.png',
          orientation: 'landscape',
          idealdimensions: {
            width: '1920',
            height: '1080'
          },
          emptyoptions: {
            fontsize: '50',
            bgcolor: '#ccc',
            textcolor: '#fff',
            textbgcolor: 'rgba(0, 0, 0, 0.7)',
            images: [],
            headline: '',
            text: ''
          }
        }];
      },
      getTemplate: function(id) {
        return this.getTemplates();
      }
    };

    // Instantiate slide controller with mocks.
    slideController = $controller("SlideController", { $scope: scope, templateFactory: mockTemplateFactory });
  }));

  describe('on load:', function() {
    it('should have loaded an empty slide', inject(function() {
      expect(scope.slide).toEqual({
        id: null,
        title: '',
        user: '',
        duration: '',
        orientation: '',
        template: '',
        created_at: parseInt((new Date().getTime()) / 1000),
        options: null
      });
    }));

    it('should have step set to 1', inject(function(){
      expect(scope.step).toBe(1);
    }));

    it('should have templates loaded', inject(function() {
      expect(scope.templates.length).toEqual(1);
    }));
  });

  describe('going through slide creation process', function() {
    it('should go to step 1 when trying to load step 2 with no slide connected', inject(function() {
      scope.goToStep(2);
      expect(scope.step).toBe(1);
    }));

    it('should go to step 2 when title has been set', inject(function() {
      scope.slide.title = "test";
      scope.submitStep();
      expect(scope.step).toBe(2)
      expect(scope.templatePath).toBe('/partials/slide/slide2.html');
    }));

    it('should go to step 3 when title and orientation has been set', inject(function() {
      scope.slide.title = 'test';
      scope.submitStep();
      scope.selectOrientation('landscape');
      scope.submitStep();
      expect(scope.step).toBe(3);
      expect(scope.templatePath).toBe('/partials/slide/slide3.html');
    }));

    it('should go through all the steps', inject(function() {
      scope.slide.title = 'test';
      scope.submitStep();
      scope.selectOrientation('landscape');
      scope.submitStep();
      scope.selectTemplate('text-top');
      scope.submitStep();
      scope.submitStep();
      scope.submitStep();
    }));
  });
});
