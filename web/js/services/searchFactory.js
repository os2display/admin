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
  this.connect = function() {
    var deferred = $q.defer();

    // Try to connect to the server if not allready connected.
    if (socket === undefined) {
      // Try to get connection to the proxy.
      getSocket(deferred);
    }
    else {
      deferred.resolve('Connected to the server.');
    }

    return deferred.promise;
  };

  this.latest = function(type) {
    //socket.emit('search', { search: '', fields: ['title'], sort: 'id', type: 'Indholdskanalen\\MainBundle\\Entity\\Screen' });
    socket.emit('search', { search: '', sort: 'id', type: 'Indholdskanalen\\MainBundle\\Entity\\Screen' });
  };

  this.on = function(eventName, callback) {
    socket.on(eventName, function() {
      var args = arguments;
      $rootScope.$apply(function() {
        callback.apply(socket, args);
      });
    });
  };

}]);
