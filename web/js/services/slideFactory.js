/**
 * Slide factory.
 */
ikApp.factory('slideFactory', ['$http', '$q', 'userFactory', function($http, $q, userFactory) {
  var factory = {};

  // Current open slide.
  // This is the slide we are editing.
  var currentSlide = null;

  /**
   * Get all slides.
   */
  factory.getSlides = function() {
    var defer = $q.defer();

    $http.get('/api/slides')
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  }

  /**
   * Find slide to edit. If id is not set return current slide, else load from backend.
   * @param id
   */
  factory.getEditSlide = function(id) {
    var defer = $q.defer();

    if (id === null || id === undefined || id === '') {
      defer.resolve(currentSlide);
    } else {
      $http.get('/api/slide/' + id)
        .success(function(data, status) {
          currentSlide = data;
          defer.resolve(currentSlide);
        })
        .error(function(data, status) {
          defer.reject(status);
        });
    }

    return defer.promise;
  }

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
  }

  /**
   * Saves slide to slides. Assigns an id, if it is not set.
   */
  factory.saveSlide = function() {
    var defer = $q.defer();

    userFactory.getCurrentUser().then(
      function(user) {
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
    );

    return defer.promise;
  }

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

    return currentSlide;
  }

  return factory;
}]);

