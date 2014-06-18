/**
 * Slide service.
 */
ikApp.factory('slideFactory', function() {
  var factory = {};
  var slides = [
    {
      id: 1,
      title: 'Et slide her',
      orientation: '',
      template: '',
      created: '1403030600',
      options: {
        'fontsize': '32',
        'bgcolor': '#ccc',
        'textcolor': '#fff',
        'textbgcolor': 'rgba(0, 0, 0, 0.7)',
        'image': '',
        'headline': '',
        'text': '',
        idealdimensions: {
          width: '1920',
          height: '1080'
        }
      }
    },
    {
      id: 2,
      title: 'Billede af en vej',
      orientation: '',
      template: '',
      created: '1405044600',
      options: {
        'fontsize': '32',
        'bgcolor': '#ccc',
        'textcolor': '#fff',
        'textbgcolor': 'rgba(0, 0, 0, 0.7)',
        'image': 'images/outlines/slide-config-default.png',
        'headline': 'Dette er en overskrift',
        'text': 'sdflksdfjdsfjsdjipf sdflksdfjdsfjsdjipf sdflksdfjdsfjsdjipf sdflksdfjdsfjsdjipf sdflksdfjdsfjsdjipf sdflksdfjdsfjsdjipf',
        idealdimensions: {
          width: '1920',
          height: '1080'
        }
      }
    },
    {
      id: 3,
      title: 'Halli hallo',
      orientation: '',
      template: '',
      created: '1401049600',
      options: {
        'fontsize': '32',
        'bgcolor': '#ccc',
        'textcolor': '#fff',
        'textbgcolor': 'rgba(0, 0, 0, 0.7)',
        'image': '',
        'headline': '',
        'text': 'dflksd fjdsfjsdjipf sdflksdfjd dflksdfjdsfjsdjipf sdflksdfjdsfjsdjipf sdf dflksdfjd sfjsdjipf sdflksdfjdsfjsdjipf sdfsfjsdjipf sdf',
        idealdimensions: {
          width: '1920',
          height: '1080'
        }
      }
    },
    {
      id: 4,
      title: 'En tur i byen',
      orientation: '',
      template: '',
      created: '1405000600',
      options: {
        'fontsize': '32',
        'bgcolor': '#ccc',
        'textcolor': '#fff',
        'textbgcolor': 'rgba(0, 0, 0, 0.7)',
        'image': '',
        'headline': '',
        'text': '',
        idealdimensions: {
          width: '1920',
          height: '1080'
        }
      }
    },
    {
      id: 5,
      title: 'En tur på stranden',
      orientation: '',
      template: '',
      created: '1402011600',
      options: {
        'fontsize': '32',
        'bgcolor': '#ccc',
        'textcolor': '#fff',
        'textbgcolor': 'rgba(0, 0, 0, 0.7)',
        'image': '',
        'headline': 'Another more text',
        'text': '',
        idealdimensions: {
          width: '1920',
          height: '1080'
        }
      }
    }
  ];
  var next_id = 6;


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
   * Get all slides.
   * @returns {Array}
   */
  factory.getSlides = function() {
    return slides;
  }


  /**
   * Find the slide with @id
   * @param id
   * @returns slide or null
   */
  factory.getSlide = function(id) {
    var arr = [];
    angular.forEach(slides, function(value, key) {
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


  /**
   * Returns an empty slide.
   * @returns slide (empty)
   */
  factory.emptySlide = function() {
    return {
      id: null,
      title: '',
      orientation: '',
      template: '',
      options: {
        fontsize: '32',
        bgcolor: '#ccc',
        textcolor: '#fff',
        textbgcolor: 'rgba(0, 0, 0, 0.7)',
        image: '',
        headline: '',
        text: '',
        idealdimensions: {
          width: '1920',
          height: '1080'
        }
      }
    };
  }


  /**
   * Saves slide to slides. Assigns an id, if it is not set.
   * @param slide
   * @returns slide
   */
  factory.saveSlide = function(slide) {
    if (slide.id === null) {
      slide.id = getNextID();
      slides.push(slide);
    } else {
      var s = factory.getSlide(slide.id);

      if (s === null) {
        slide.id = getNextID();
        slides.push(slide);
      } else {
        s = slide;
      }
    }
    return slide;
  }

  return factory;
});

