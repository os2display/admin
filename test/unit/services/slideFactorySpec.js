'use strict';

describe('SlideFactory', function(){
  var $httpBackend;

  beforeEach(module('ikApp'));

  beforeEach(inject(function($injector) {
    $httpBackend = $injector.get('$httpBackend');
    $httpBackend.when('GET', '/api/slides').respond([{
        id: id,
        title: 'Test 1',
        orientation: 'landscape',
        template: 'text-top',
        created: 1403702897,
        options: {
          fontsize: '32',
          bgcolor: '#ccc',
          textcolor: '#fff',
          textbgcolor: 'rgba(0, 0, 0, 0.7)',
          image: '',
          headline: 'Title',
          text: 'Text text',
          idealdimensions: {
            width: '1920',
            height: '1080'
          }
        }
      }]
    );
  }));

  afterEach(function() {
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  describe('getSlides', function() {
    it('should fetch authentication token', function() {
      $httpBackend.expectGET('/auth.py');
      var controller = createController();
      $httpBackend.flush();
    });

  });

});
