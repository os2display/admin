ikApp.factory('templateFactory', function() {
  var factory = {};
  var templates = [
    {
      id: 'text-top',
      image: '/ik-templates/text-top/text-top.png',
      orientation: 'landscape'
    },
    {
      id: 'text-bottom',
      image: '/ik-templates/text-bottom/text-bottom.png',
      orientation: 'landscape'
    },
    {
      id: 'text-left',
      image: '/ik-templates/text-left/text-left.png',
      orientation: 'landscape'
    },
    {
      id: 'text-right',
      image: '/ik-templates/text-right/text-right.png',
      orientation: 'landscape'
    },
    {
      id: 'portrait-text-top',
      image: '/ik-templates/portrait-text-top/portrait-text-top.png',
      orientation: 'portrait'
    }
  ];

  factory.getTemplates = function() {
    return templates;
  }

  factory.getTemplate = function(id) {
    var arr = [];
    angular.forEach(templates, function(value, key) {
      if (value['id'] == id) {
        arr.push(value);
      }
    })

    if (arr.length === 0) {
      return null;
    } else {
      return arr[0];
    }
  }

  return factory;
});