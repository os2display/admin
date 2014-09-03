/**
 * Slide factory.
 */
ikApp.factory('slideFactory', ['$http', '$q', 'userFactory', 'searchFactory', function($http, $q, userFactory, searchFactory) {
  var factory = {};

  // Current open slide.
  // This is the slide we are editing.
  var currentSlide = null;

  factory.searchSlides = function(search) {
    search.type = 'Indholdskanalen\\MainBundle\\Entity\\Slide';
    search.app_id = 1234;

    return searchFactory.search(search);
  };

  /**
   * Get all slides.
   */
  factory.getSlides = function getSlides() {
    var defer = $q.defer();

    $http.get('/api/slides')
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  };

  /**
   * Find slide to edit. If id is not set return current slide, else load from backend.
   * @param id
   */
  factory.getEditSlide = function getEditSlide(id) {
    var defer = $q.defer();

    if (id === null || id === undefined || id === '') {
      defer.resolve(currentSlide);
    }
    else {
      if (currentSlide !== null && currentSlide.id == id) {
        defer.resolve(currentSlide);
      }
      else {
        $http.get('/api/slide/' + id)
          .success(function(data, status) {
            currentSlide = data;
            defer.resolve(currentSlide);
          })
          .error(function(data, status) {
            defer.reject(status);
          });
      }
    }

    return defer.promise;
  };

  /**
   * Find the slide with @id
   * @param id
   */
  factory.getSlide = function(id) {
    var defer = $q.defer();

    $http.get('/api/slide/' + id)
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  };

  /**
   * Saves slide to slides. Assigns an id, if it is not set.
   */
  factory.saveSlide = function() {
    var defer = $q.defer();

    userFactory.getCurrentUser().then(
      function(user) {
        if (currentSlide === null) {
          defer.reject(404);

        } else {
          currentSlide.user = user.id;

          $http.post('/api/slide', currentSlide)
            .success(function(data, status) {
              defer.resolve(data);
              currentSlide = null;
            })
            .error(function(data, status) {
              defer.reject(status);
            });
        }
      }
    );

    return defer.promise;
  };

  /**
   * Returns an empty slide.
   * @returns slide (empty)
   */
  factory.emptySlide = function() {
    currentSlide = {
      id: null,
      title: '',
      user: '',
      orientation: '',
      template: '',
      created_at: parseInt((new Date().getTime()) / 1000),
      options: null,
      duration: '15' // Seconds.
    };

    return currentSlide;
  };

  return factory;
}]);

