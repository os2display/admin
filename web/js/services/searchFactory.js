/**
 * @file
 * Search factory that handles communication with search engine.
 *
 * The communication is based on web-sockets via socket.io library.
 */
ikApp.service('searchFactory', ['$q', '$rootScope', function($q, $rootScope) {
  var socket;
  var self = this;

  /**
   * Connect to the web-socket.
   *
   * @param deferred
   *   The is a deferred object that should be resovled on connection.
   */
  function getSocket(deferred) {
    // Get connected to the server.
    socket = io.connect('http://service.indholdskanalen.vm:3001');

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
      // @todo: re-connection is automatically handled by socket.io library,
      // but we might need to stop sending request until reconnection or the
      // request will be queued and send all at once... which could give some
      // strange side effects in the application if not handled.
    });
  }

  /**
   * Create the connection to the server.
   *
   * @return {promise}
   *   An promise is return that will be resolved on connection.
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

  /**
   * Send search request to the engine.
   *
   * The default search should have this form:
   *
   * {
   *   "fields": 'title',
   *     "text": '',
   *     "sort": [
   *      {
   *       "created_at" : {
   *         "order": "desc"
   *       }
   *     }
   *     ]
   *   }
   * }
   *
   * @param search
   *   This is a json object as described above as default.
   *
   * @returns {promise}
   *   When data is received from the backend. If no data found an empty JSON
   *   object is retuned.
   */
  this.search = function(search) {
    var deferred = $q.defer();

    connect().then(function () {
      socket.emit('search', search);
      socket.on('result', function (data) {
        deferred.resolve(data);
      });
    });

    return deferred.promise;
  };
}]);
