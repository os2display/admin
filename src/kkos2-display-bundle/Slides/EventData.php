<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;


use DateTime;

class EventData
{

  public function extractData($data)
  {
    $date = DateTime::createFromFormat('d.m.Y', current($data['startdate']));
    $events = [
      'title' => $data['title'],
      'body' => $data['field_teaser'],
      'image' => $data['image'],
      'place' => $data['field_display_institution'],
      'date' => $this->getDayName($date) . ' d. ' . $date->format('j') . '. ' . $this->getMonthName($date),
        'time' => current($data['time']),
    ];
    return array_map('trim', $events);
  }

  protected function getDayName(DateTime $date)
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

  protected function getMonthName(DateTime $date)
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
