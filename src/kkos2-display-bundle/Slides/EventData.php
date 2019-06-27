<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;


use DateTime;

class EventData
{

  /**
   * List of fields that where missing when importing data.
   * @var array missing
   */
  protected $missing = [];

  public function hasMissing() {
    return count($this->missing) > 0;
  }

  public function getMissing() {
    return $this->missing;
  }

  protected function logStatus($logger) {
    if ($this->hasMissing()) {
      foreach ($this->missing as $missing) {
        $this->logger->warning('Missing ' . implode(', ', $missing));
      }
    }
  }

  public function extractData($data)
  {
    $expected_keys = ['startdate', 'title', 'field_teaser', 'image', 'field_display_institution', 'time'];
    $missing = array_diff($expected_keys, array_keys($data));
    if (count($missing) > 0){
      $this->missing[] = $missing;
      return [];
    }

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
