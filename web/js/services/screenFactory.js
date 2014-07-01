/**
 * Screen factory.
 */
ikApp.factory('screenFactory', ['$http', '$q', function($http, $q) {
  var factory = {};
  var currentGroup = null;
  var currentScreen = null;

  /**
   * Get all screens.
   * @returns {Array}
   */
  factory.getScreens = function() {
    var defer = $q.defer();

    $http.get('/api/screens')
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  }

  factory.getEditScreen = function(id) {
    var defer = $q.defer();

    if (id === null || id === undefined || id === '') {
      defer.resolve(currentScreen);
    } else {
      $http.get('/api/screen/' + id)
        .success(function(data, status) {
          currentScreen = data;
          defer.resolve(currentScreen);
        })
        .error(function(data, status) {
          defer.reject(status);
        });
    }

    return defer.promise;
  }


  /**
   * Get all screen groups.
   * @returns {Array}
   */
  factory.getScreenGroups = function() {
    var defer = $q.defer();

    $http.get('/api/screen_groups')
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  }


  /**
   * Find the screen with @id
   * @param id
   * @returns screen or null
   */
  factory.getScreen = function(id) {
    var defer = $q.defer();

    $http.get('/api/screen/' + id)
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  }

  /**
   * Find the screenGroup with @id
   * @param id
   * @returns screen or null
   */
  factory.getScreenGroup = function(id) {
    var defer = $q.defer();

    $http.get('/api/screen_group/' + id)
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  }

  /**
   * Find the screen groups that screen with @id is part of
   * @param id
   * @returns group or null
   */
  factory.getScreenScreenGroups = function(id) {
    var defer = $q.defer();

    $http.get('/api/screen/' + id + '/screen_groups')
      .success(function(data, status) {
        defer.resolve(data);
      })
      .error(function(data, status) {
        defer.reject(status);
      });

    return defer.promise;
  }


  /**
   * Saves screen.
   */
  factory.saveScreen = function() {
    var defer = $q.defer();

    if (currentScreen === null) {
      defer.reject(404);
    } else {
      if (currentScreen.width > currentScreen.height) {
        currentScreen.orientation = 'landscape';
      } else {
        currentScreen.orientation = 'portrait';
      }

      $http.post('/api/screen', currentScreen)
        .success(function(data, status) {
          defer.resolve(data);
          currentScreen = null;
        })
        .error(function(data, status) {
          defer.reject(status);
        });
    }

    return defer.promise;
  }

  /**
   * Returns an empty screen.
   * @returns screen (empty)
   */
  factory.emptyScreen = function() {
    currentScreen = {
      id: null,
      title: '',
      orientation: '',
      width: '',
      height: '',
      created: parseInt((new Date().getTime()) / 1000),
      groups: []
    };

    return currentScreen;
  }


  /**
   * Returns an empty group.
   * @returns group (empty)
   */
  factory.emptyScreenGroup = function() {
    currentGroup = {
      id: null,
      title: '',
      screens: [],
      created: parseInt((new Date().getTime()) / 1000)
    };

    return currentGroup;
  }

  return factory;
}]);

