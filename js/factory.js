ikApp.factory('slideFactory', function() {
  var factory = {};

  factory.getSlide = function(id) {
    if (id === "1") {
      return {
        id: '1',
        title: 'fisk',
        orientation: 'w',
        template: '1',
        options: []
      };
    } else {
      return {
        id: '',
        title: '',
        orientation: '',
        template: '',
        options: []
      };
    }
  }

  return factory;
});