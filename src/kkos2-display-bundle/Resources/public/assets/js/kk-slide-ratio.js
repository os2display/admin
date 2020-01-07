if (!window.kkSlideRatio) {
  window.kkSlideRatio = {};
}
if (!window.kkSlideRatio.getRatio) {

  // TODO. Docs
  window.kkSlideRatio.getRatio = function () {
    const width  = window.innerWidth || document.documentElement.clientWidth ||
      document.body.clientWidth;
    const height = window.innerHeight|| document.documentElement.clientHeight||
      document.body.clientHeight;

    let ratio = (height > width) ? 'vertical' : 'horizontal';
    if (document.querySelectorAll('.half-split').length > 0) {
      ratio = '8-9';
    } else if (document.querySelectorAll('.two-rows-portrait').length > 0) {
      ratio = '9-8';
    }
    return ratio;
  };
}

