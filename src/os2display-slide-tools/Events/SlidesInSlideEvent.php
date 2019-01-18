<?php

namespace Reload\Os2DisplaySlideTools\Events;


use Reload\Os2DisplaySlideTools\Slides\SlidesInSlide;
use Symfony\Component\EventDispatcher\Event;

class SlidesInSlideEvent extends Event
{
  /** @var SlidesInSlide */
  protected $slidesInSlide;

  /** ... */
  public function __construct(SlidesInSlide $slidesInSlide)
  {
    $this->slidesInSlide = $slidesInSlide;
  }

  public function getSlidesInSlide()
  {
    return $this->slidesInSlide;
  }

  /** ... */
  public function getSubSlides()
  {
    return $this->slidesInSlide->getSubslides();
  }

}