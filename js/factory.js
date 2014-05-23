/**
 * Slide service.
 */
ikApp.factory('slideFactory', function($filter) {
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
      options: []
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