/**
 * Search factory.
 */
ikApp.service('searchFactory', ['$q', '$rootScope', function($q, $rootScope) {
  var socket;
  var self = this;


  /**
   * Connect to the web-socket.
   *
   * @param string token
   *   JWT authentication token from the activation request.
   */
  function getSocket(deferred) {
    // Get connected to the server.
    socket = io.connect('http://service.indholdskanalen.vm:3000');

    // Handle error events.
    socket.on('error', function (reason) {
      deferred.reject(reason);
    });

    socket.on('connect', function(data) {
      self.connected = true;
      deferred.resolve('Connected to the server.');
    });

    // Handle disconnect event (fires when disconnected or connection fails).
    socket.on('disconnect', function (reason) {
      if (reason === 'booted') {
        // Reload application.
        // TODO: Do a connect reload.
      }
    });
  }

  /**
   * Create the connection to the server with promise.
   */
  function connect() {
    // Try to connect to the server if not already connected.
    var deferred = $q.defer();
    if (socket === undefined) {
      // Try to get connection to the proxy.
      getSocket(deferred);
    }
    else {
      deferred.resolve('Connected to the server.');
    }

    return deferred.promise;
  }

  this.search = function(search) {
    var deferred = $q.defer();
    connect().then(function () {
      socket.emit('search', search);
      socket.once('result', function (data) {
        deferred.resolve(data);
      });
    });

    return deferred.promise;
  };

  this.latest = function(search) {
    var deferred = $q.defer();
    connect().then(function () {
      socket.emit('search', { text: '', sort: 'created', type: search.type, app_id: search.app_id });
      socket.once('result', function (data) {
        deferred.resolve(data);
      });
    });

    return deferred.promise;
  };
}]);
