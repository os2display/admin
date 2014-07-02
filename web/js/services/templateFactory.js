ikApp.factory('templateFactory', function() {
  var factory = {};
  var templates = [
    {
      id: 'text-top',
      image: '/ik-templates/text-top/text-top.png',
      orientation: 'landscape',
      idealdimensions: {
        width: '1920',
        height: '1080'
      },
      emptyoptions: {
        fontsize: '32',
        bgcolor: '#ccc',
        textcolor: '#fff',
        textbgcolor: 'rgba(0, 0, 0, 0.7)',
        image: '',
        headline: '',
        text: ''
      }
    },
    {
      id: 'text-bottom',
      image: '/ik-templates/text-bottom/text-bottom.png',
      orientation: 'landscape',
      idealdimensions: {
        width: '1920',
        height: '1080'
      },
      emptyoptions: {
        fontsize: '32',
        bgcolor: '#ccc',
        textcolor: '#fff',
        textbgcolor: 'rgba(0, 0, 0, 0.7)',
        image: '',
        headline: '',
        text: ''
      }

    },
    {
      id: 'text-left',
      image: '/ik-templates/text-left/text-left.png',
      orientation: 'landscape',
      idealdimensions: {
        width: '1920',
        height: '1080'
      },
      emptyoptions: {
        fontsize: '32',
        bgcolor: '#ccc',
        textcolor: '#fff',
        textbgcolor: 'rgba(0, 0, 0, 0.7)',
        image: '',
        headline: '',
        text: ''
      }

    },
    {
      id: 'text-right',
      image: '/ik-templates/text-right/text-right.png',
      orientation: 'landscape',
      idealdimensions: {
        width: '1920',
        height: '1080'
      },
      emptyoptions: {
        fontsize: '32',
        bgcolor: '#ccc',
        textcolor: '#fff',
        textbgcolor: 'rgba(0, 0, 0, 0.7)',
        image: '',
        headline: '',
        text: ''
      }

    },
    {
      id: 'only-image',
      image: '/ik-templates/only-image/only-image.png',
      orientation: 'landscape',
      idealdimensions: {
        width: '1920',
        height: '1080'
      },
      emptyoptions: {
        bgcolor: '#ccc',
        image: ''
      }
    },
    {
      id: 'portrait-text-top',
      image: '/ik-templates/portrait-text-top/portrait-text-top.png',
      orientation: 'portrait',
      idealdimensions: {
        width: '1080',
        height: '1920'
      },
      emptyoptions: {
        fontsize: '32',
        bgcolor: '#ccc',
        textcolor: '#fff',
        textbgcolor: 'rgba(0, 0, 0, 0.7)',
        image: '',
        headline: '',
        text: ''
      }

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