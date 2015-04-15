<?php
/**
 * @file
 * Slide model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CalendarSlide
 * @package Indholdskanalen\MainBundle\Entity
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class CalendarSlide extends Slide {
  /**
   * @ORM\Column(name="calendar_events", type="json_array", nullable=true)
   */
  protected $calendarEvents;

  /**
   * @ORM\Column(name="interest_period", type="string", nullable=true)
   *
   * Possible options: "today", "week", "month", null (all)
   */
  protected $interestPeriod;


  public function getCalendarEvents() {
    return $this->calendarEvents;
  }

  public function setCalendarEvents($calendarEvents) {
    $this->calendarEvents = $calendarEvents;
  }

  public function getInterestPeriod() {
    return $this->interestPeriod;
  }

  public function setInterestPeriod($interestPeriod) {
    $this->interestPeriod = $interestPeriod;
  }
}
