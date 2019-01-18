<?php

namespace Reload\Os2DisplaySlideTools\Slides;


use Os2Display\CoreBundle\Entity\Slide;

class SlidesInSlide
{

  private $slide;

  private $subslides = NULL;

  public function __construct(Slide $slide)
  {
    $this->slide = $slide;
  }

  public function getOption($key, $defaultValue = false)
  {
    $options = $this->slide->getOptions();
    if (empty($options[$key])) {
      return $defaultValue;
    }
    return $options[$key];
  }

  public function getSubslides()
  {
    return $this->subslides;
  }

  public function setSubslides(array $subslides)
  {
    $this->subslides = $subslides;
  }

}