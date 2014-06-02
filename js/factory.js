/**
 * Slide service.
 */
ikApp.factory('slideFactory', function() {
  var factory = {};
  var slides = [];
  var next_id = 0;

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
        'bgcolor': '#ccc',
        'textcolor': '#fff',
        'textbgcolor': '#000',
        'image': '',
        'headline': '',
        'text': ''
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


ikApp.factory('templateFactory', function() {
  var factory = {};
  var templates = [
    {
      id: 1,
      src: '/images/outlines/template-example-1.png'
    },
    {
      id: 2,
      src: '/images/outlines/template-example-2.png'
    },
    {
      id: 3,
      src: '/images/outlines/template-example-3.png'
    },
    {
      id: 4,
      src: '/images/outlines/template-example-4.png'
    },
    {
      id: 5,
      src: '/images/outlines/template-example-5.png'
    }
  ];

  factory.getTemplates = function() {
    return templates;
  }

  return factory;
});

ikApp.factory('imageFactory', function() {
  var factory = {};
  var images = [
    {
      title: 'road',
      url: '/images/outlines/slide-config-default.png'
    }
  ];

  factory.getImages = function() {
    return images;
  }

  return factory;
});