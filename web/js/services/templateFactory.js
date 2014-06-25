ikApp.factory('templateFactory', function() {
  var factory = {};
  var templates = [
    {
      id: 'text-top',
      html: '/ik-templates/text-top/text-top.html',
      image: '/ik-templates/text-top/text-top.png',
      orientation: 'landscape'
    },
    {
      id: 'text-bottom',
      html: '/ik-templates/text-bottom/text-bottom.html',
      image: '/ik-templates/text-bottom/text-bottom.png',
      orientation: 'landscape'
    },
    {
      id: 'text-left',
      html: '/ik-templates/text-left/text-left.html',
      image: '/ik-templates/text-left/text-left.png',
      orientation: 'landscape'
    },
    {
      id: 'text-right',
      html: '/ik-templates/text-right/text-right.html',
      image: '/ik-templates/text-right/text-right.png',
      orientation: 'landscape'
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