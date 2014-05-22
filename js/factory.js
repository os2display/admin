ikApp.factory('slideFactory', function() {
  var factory = {};

  factory.getSlide = function(id) {
    if (id === "1") {
      return {
        title: 'fisk',
        orientation: 'wide',
        template: '1',
        options: []
      };
    } else {
      return {title: ''};
    }
  }

  return factory;
});