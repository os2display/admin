ikApp.factory('channelFactory', ['$http', '$q', function($http, $q) {
  var factory = {};
  var channels = [
    {
      id: 1,
      title: 'My channel',
      created: parseInt((new Date().getTime()) / 1000),
      orientation: 'landscape',
      slides: [1,2,3]
    }
  ];
  var next_id = 2;


  /**
   * Internal function to get next id.
   * @returns id
   */
  function getNextID() {
    var i  = next_id;
    next_id = i + 1;

    return i;
  }


  /**
   * Get channels.
   */
  factory.getChannels = function() {
    var defer = $q.defer();

    defer.resolve(channels);

    return defer.promise;
  }


  /**
   * Find the channel with @id
   * @param id
   * @returns channel or null
   */
  factory.getChannel = function(id) {
    var defer = $q.defer();

    var arr = [];
    angular.forEach(channels, function(value, key) {
      if (value.id == id) {
        arr.push(value);
      }
    })

    if (arr.length === 0) {
      defer.reject();
    } else {
      defer.resolve(arr[0]);
    }

    return defer.promise;
  }


  /**
   * Returns an empty channel.
   * @returns channel (empty)
   */
  factory.emptyChannel = function() {
    return {
      id: null,
      title: '',
      orientation: '',
      created: parseInt((new Date().getTime()) / 1000),
      slides: []
    };
  }


  /**
   * Saves channel to channels. Assigns an id, if it is not set.
   * @param channel
   * @returns channel
   */
  factory.saveChannel = function(channel) {
    if (channel.id === null) {
      channel.id = getNextID();
      channels.push(channel);
    } else {
      var s = factory.getChannel(channel.id);

      if (s === null) {
        channel.id = getNextID();
        channel.push(channel);
      } else {
        s = channel;
      }
    }
    return channel;
  }

  return factory;
}]);
