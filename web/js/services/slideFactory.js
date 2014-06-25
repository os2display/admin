/**
 * Slide service.
 */
ikApp.factory('slideFactory', ['$http', '$q', function($http, $q) {
  var factory = {};

  // Current open slide.
  // This is the slide we are editing.
  factory.currentSlide = null;

  /**
   * Get all slides.
   * @returns {Array}
   */
  factory.getSlides = function() {
    var defer = $q.defer();

    $http.get('/api/slides')
      .success(function(data) {
        defer.resolve(data);
      })
      .error(function() {
        defer.reject();
      });

    return defer.promise;
  }

  factory.getEditSlide = function(id) {
    var defer = $q.defer();

    if (id === null || id === undefined || id === '') {
      defer.resolve(factory.currentSlide);
    } else {
      $http.get('/api/slide/' + id)
        .success(function(data) {
          factory.currentSlide = data;
          defer.resolve(factory.currentSlide);
        })
        .error(function() {
          defer.reject();
        });
    }

    return defer.promise;
  }

  /**
   * Find the slide with @id
   * @param id
   * @returns slide or null
   */
  factory.getSlide = function(id) {
    var defer = $q.defer();

    $http.get('/api/slide/get/' + id)
      .success(function(data) {
        defer.resolve(data);
      })
      .error(function() {
        defer.reject();
      });

    return defer.promise;
  }

  /**
   * Saves slide to slides. Assigns an id, if it is not set.
   */
  factory.saveSlide = function() {
    var defer = $q.defer();

    console.log(factory.currentSlide);

    $http.post('/api/slide/save', factory.currentSlide)
      .success(function(data) {
        console.log(data);
        defer.resolve("success");
        factory.currentSlide = null;
      })
      .error(function() {
        console.log("error");
        defer.reject("failure");
      });

    return defer.promise;
  }

  /**
   * Returns an empty slide.
   * @returns slide (empty)
   */
  factory.emptySlide = function() {
    factory.currentSlide = {
      id: null,
      title: '',
      orientation: '',
      template: '',
      created: parseInt((new Date().getTime()) / 1000),
      options: {
        fontsize: '32',
        bgcolor: '#ccc',
        textcolor: '#fff',
        textbgcolor: 'rgba(0, 0, 0, 0.7)',
        image: '',
        headline: '',
        text: '',
        idealdimensions: {
          width: '1920',
          height: '1080'
        }
      }
    };

    return factory.currentSlide;
  }

  return factory;
}]);

