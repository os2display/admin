/**
 * @file
 * Contains the slide factory.
 */

/**
 * Slide factory. Main entry point for slides.
 */
ikApp.factory('slideFactory', ['$http', '$q', 'searchFactory',
  function($http, $q, searchFactory) {
    var factory = {};

    // Currently open slide.
    // This is the slide we are editing.
    var currentSlide = null;

    /**
     * Search via search_node.
     * @param search
     * @returns {*|Number}
     */
    factory.searchSlides = function(search) {
      search.type = 'Indholdskanalen\\MainBundle\\Entity\\Slide';
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
     * Load the slides with the given ids.
     *
     * @param ids
     */
    factory.loadSlidesBulk = function loadSlidesBulk(ids) {
      var defer = $q.defer();

      // Build query string.
      var queryString = "?";
      for (var i = 0; i < ids.length; i++) {
        queryString = queryString + "ids[]=" + ids[i];
        if (i < ids.length - 1) {
          queryString = queryString + "&"
        }
      }

      // Load bulk.
      $http.get('/api/slides/bulk' + queryString)
        .success(function(data, status) {
          defer.resolve(data);
        })
        .error(function(data, status) {
          defer.reject(status)
        });

      return defer.promise;
    };

    /**
     * Clear currently slide.
     */
    factory.clearCurrentSlide = function clearCurrentSlide() {
      currentSlide = null;
    }

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
     * Saves slide to slides.
     */
    factory.saveSlide = function() {
      var defer = $q.defer();

      $http.post('/api/slide', currentSlide)
        .success(function(data) {
          defer.resolve(data);
          currentSlide = null;
        })
        .error(function(data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Returns an empty slide.
     * @returns slide (empty)
     */
    factory.emptySlide = function() {
      currentSlide = {
        "id": null,
        "published": false,
        "schedule_from": null,
        "schedule_to": null,
        "media": [],
        "media_type": null,
        "title": '',
        "user": '',
        "duration": '',
        "orientation": '',
        "template": '',
        "options": null
      };

      return currentSlide;
    };

    return factory;
  }
]);

