<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;


use DateTime;

trait EventTrait {

  public function getDayName(DateTime $date)
  {
    $days = [
      'Søndag',
      'Mandag',
      'Tirsdag',
      'Onsdag',
      'Torsdag',
      'Fredag',
      'Lørdag',
    ];
    return $days[$date->format('w')];
  }

  public function getMonthName(DateTime $date)
  {
    $months = [
      'not a month',
      'januar',
      'februar',
      'marts',
      'april',
      'maj',
      'juni',
      'juli',
      'august',
      'september',
      'oktober',
      'november',
      'december',
    ];
    return $months[$date->format('n')];
  }
}
