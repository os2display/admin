'use strict';

describe('SlideFactory', function(){
  var $httpBackend;
  var slideFactory;

  beforeEach(module('ikApp'));

  beforeEach(function(){
    inject(function($injector) {
      $httpBackend  = $injector.get('$httpBackend');

      $httpBackend.whenGET('/api/slides').respond(
        [{
          id: 1,
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
        },
          {
            id: 2,
            title: 'Test 2',
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

      $httpBackend.whenGET('/api/slide/1').respond(
        {
          id: 1,
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
        }
      );

      $httpBackend.whenGET('/api/slide/200').respond(function(method, url, data, headers) {
        return [404, {}, {}];
      });

      $httpBackend.whenPOST('/api/slide').respond(function(method, url, data, headers) {
        if (data == null) {
          return [404, null, {}];
        }
        var d = angular.fromJson(data);
        d.id = 1;

        return [200, angular.toJson(d), {}];
      });

      slideFactory = $injector.get('slideFactory');
    });
  });

  afterEach(function() {
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it("should return a list of 2 slides", inject(function() {
    slideFactory.getSlides().then(function(data) {
      expect(data.length).toEqual(2);
    });
    $httpBackend.flush();
  }));

  it("should return the slide with id 1", inject(function() {
    slideFactory.getSlide(1).then(function(data) {
      expect(data.id).toEqual(1);
    });
    $httpBackend.flush();
  }));

  it("getting an empty slide should return a slide with id==null", inject(function() {
    var slide = slideFactory.emptySlide();
    expect(slide.id).toBeNull();
  }));

  it("getting an empty slide and saving it should yield a slide with id == 1", inject(function() {
    var slide = slideFactory.emptySlide();
    slideFactory.saveSlide().then(function(data) {
      expect(data.id).toEqual(1);
    });
    $httpBackend.flush();
  }));

  it("saving a slide where there is not set a current slide, should result in error", inject(function() {
    slideFactory.saveSlide().then(function(data) {
      expect(data).toEqual("error");
    });
    $httpBackend.flush();
  }));

  it("should return null for getEditSlide with no id set", inject(function() {
    slideFactory.getEditSlide().then(function(data) {
      expect(data).toBeNull();
    });
  }));

/*  NOT DONE!!
  it("should return 404 for getting a slide that does not exist", inject(function() {
    slideFactory.getSlide(200).then(function(data) {
    }, function(reason) {
      expect().toEqual('error');
    });
    $httpBackend.flush();
  }));*/
});
