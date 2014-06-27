'use strict';

describe('SlideDirectives', function(){
  var $httpBackend;

  beforeEach(module('ikApp'));

  beforeEach(inject(function($injector) {
    $httpBackend = $injector.get('$httpBackend');
    $httpBackend.when('GET', '/api/slide/get', id).respond({
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
    });
  }));
});
