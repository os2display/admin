if (!window.kkSlideRatio) {
  window.kkSlideRatio = {};
}
if (!window.kkSlideRatio.getRatio) {

  // TODO. Docs
  window.kkSlideRatio.getRatio = function () {
    var ratio = (window.screen.height > window.screen.width) ? '9-16' : '16-9';
    if (document.querySelectorAll('.half-split').length > 0) {
      ratio = '9-8';
    } else if (document.querySelectorAll('.two-rows-portrait').length > 0) {
      ratio = '8-9';
    }
    return ratio;
  };
}

