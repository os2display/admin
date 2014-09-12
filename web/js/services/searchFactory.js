/**
 * @file
 * Contains the search factory.
 */

/**
 * Search factory that handles communication with search engine.
 *
 * The communication is based on web-sockets via socket.io library.
 */
ikApp.service('searchFactory', ['$q', '$rootScope', 'configuration', function($q, $rootScope, configuration) {
  var socket;
  var self = this;

  /**
   * Connect to the web-socket.
   *
   * @param deferred
   *   The is a deferred object that should be resolved on connection.
   */
  function getSocket(deferred) {
    // Get connected to the server.
    socket = io.connect(configuration.search.address);

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
   *       "created_at.raw" : {
   *         "order": "desc"
   *       }
   *     }
   *     ],
   *     "filter": [ ]
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

    // Build default match all search query.
    var query = {
      "customer_id": configuration.search.customer_id,
      "type": search.type,
      "query": {
        "match_all": {}
      }
    };

    // Text given build field search query.
    // The analyser ensures that we match the who text string sent not part
    // of. @TODO: It this the right behaviour.
    if (search.text !== undefined && search.text !== '') {
      query.query = {
        "multi_match": {
          "query": search.text,
          "fields": search.fields,
          "analyzer": 'string_search'
        }
      };
    }

    // Add sort
    query.sort = search.sort;

    // Add filter.
    // @TODO: move to the start.
    if (search.filter !== undefined) {
      var filter = {
        "filtered": {
          "query": query.query,
          "filter": search.filter
        }
      };

      // Swap the filter.
      query.query = filter;
    }

    // @TODO: Fix sort on text fields.
//      sort: { 'name.raw': { order: 'asc' } } }

    connect().then(function () {
      socket.emit('search', query);
      socket.on('result', function (hits) {
        deferred.resolve(hits);
      });
    });

    return deferred.promise;
  };
}]);
