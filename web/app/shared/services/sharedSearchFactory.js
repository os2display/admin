/**
 * @file
 * Contains the search factory.
 */

/**
 * Shared search factory that handles communication with share search engine.
 *
 * The communication is based on web-sockets via socket.io library.
 */
angular.module('ikApp').service('sharedSearchFactory', ['$q', '$rootScope', 'configuration', '$http', 'itkLogFactory',
  function ($q, $rootScope, configuration, $http, itkLogFactory) {
    'use strict';

    var socket;
    var token = null;

    /**
     * Connect to the web-socket.
     */
    function getSocket(deferred) {
      // Get connected to the server.
      socket = io.connect(configuration.sharingService.address, {
        'query': 'token=' + token,
        'force new connection': true,
        'max reconnection attempts': Infinity
      });

      // Handle error events.
      socket.on('error', function (reason) {
        itkLogFactory.error(error.message, 'Search socket error.');
        deferred.reject(reason);
      });

      socket.on('connect', function () {
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
        if (token !== null) {
          getSocket(deferred);
        }
        else {
          $http.get('api/auth/sharing')
            .success(function (data) {
              token = data.token;
              getSocket(deferred);
            })
            .error(function (data, status) {
              itkLogFactory.error(data, 'Authentication (sharing) to search node failed (' + status + ')');
              deferred.reject(status);
            });
        }
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
     * @param index
     *   Which index to search in.
     *
     * @returns {promise}
     *   When data is received from the backend. If no data found an empty JSON
     *   object is returned.
     */
    this.search = function (search, index) {
      var deferred = $q.defer();

      // Build default match all search query.
      var query = {
        "index": index,
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
        query.query = {
          "filtered": {
            "query": query.query,
            "filter": search.filter
          }
        };
      }

      // Add pager to the query.
      if (search.hasOwnProperty('pager')) {
        query.size = search.pager.size;
        query.from = search.pager.page * search.pager.size;
      }

      connect().then(function () {
        socket.emit('search', query);
        socket.on('result', function (hits) {
          deferred.resolve(hits);
        });

        // Catch search errors.
        socket.on('searchError', function (error) {
          itkLogFactory.error('Search error', error.message);
          deferred.reject(error.message);
        });
      });

      return deferred.promise;
    };
  }
]);
