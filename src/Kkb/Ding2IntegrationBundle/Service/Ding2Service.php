<?php
/**
 * @file
 * Contains the DING2 service.
 *
 * Provides integration service with DING2.
 */

namespace Kkb\Ding2IntegrationBundle\Service;

use DateTime;
use Doctrine\Common\Persistence\ObjectRepository;
use Indholdskanalen\MainBundle\Entity\Slide;
use Indholdskanalen\MainBundle\Events\CronEvent;

/**
 * Class Din2Service
 * @package Kkb\Ding2IntegrationBundle\Service
 */
class Ding2Service {
  private $logger;
  private $container;
  private $entityManager;
  private $slideRepo;
  private $dingUrl;
  private $initialized = FALSE;
  const OPENING_HOURS_FEED_PATH = '/opening_hours/instances?from_date=%from%&to_date=%to%&nid=%nid%';
  const DING_EVENTS_FEED_PATH = '/kultunaut_export/%slug%';
  // Eg. "fredag d. 9. december 2016"
  const HUMAN_DATE_FORMAT_FULL = "%A d. %e. %B %Y";
  protected $today;
  protected $today_formatted;
  protected $tomorrow;
  protected $tomorrow_formatted;

  /**
   * A time-interval can have a notice associated, this is used when
   * eg a library has a printing service that has a different availability
   * than the rest of the library.
   *
   * This array maps location types (we currently only support "library")
   * with a list of notice-values that is expected to attached to the time
   * interval for the entire location.
   *
   * @var array
   */
  private $open_notice_values = array();
  private $service_notice_values = array();

  /**
   * Constructor.
   *
   * @param $container
   */
  public function __construct($container) {
    $this->tomorrow = new DateTime('tomorrow');
    $this->today = new DateTime('today');
    $this->tomorrow_formatted = $this->tomorrow->format('Y-m-d');
    $this->today_formatted = $this->today->format('Y-m-d');

    $this->container = $container;
    $this->entityManager = $this->container->get('doctrine')->getManager();
    $this->slideRepo = $container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide');
    $this->logger = $this->container->get('logger');

    // Setup the base url we're going to use to fetch the feeds.
    if ($this->container->hasParameter('ding_url')) {
      $this->dingUrl = $this->container->getParameter('ding_url');
      // We're unable to fetch anything if we don't have a valid Ding2 url.
      $this->initialized = TRUE;
    }

    // Get the keywords we should be looking for to identify service and
    // open hours.
    if ($this->container->hasParameter('ding_opening_hours_library_open_keys')) {
      $keys = explode(',', $this->container->getParameter('ding_opening_hours_library_open_keys'));
      $this->open_notice_values = array_map('trim', $keys);
    }
    if ($this->container->hasParameter('ding_opening_hours_library_service_keys')) {
      $keys = explode(',', $this->container->getParameter('ding_opening_hours_library_service_keys'));
      $this->service_notice_values = array_map('trim', $keys);
    }
  }

  /**
   * ik.onCron event listener.
   *
   * Updates calendar slides.
   *
   * @param CronEvent $event
   */
  public function onCron(CronEvent $event) {
    if ($this->initialized) {

      $old_locale = setlocale(LC_TIME, 0);
      // Setup a time-stamp to display at the top of the screen. We want
      setlocale(LC_TIME, "da_DK.utf8");
      $this->updateOpeningHours();
      $this->updateDingEvents();
      setlocale(LC_TIME, $old_locale);
    } 
  }

  /**
   * Locate all opening-hours slides and download their feeds.
   */
  protected function updateOpeningHours() {
    /** @var Slide[] $slide */
    $slides = $this->slideRepo->findBySlideType('opening-hours');

    $today = new DateTime('today');
    $today_string = $today->format('Y-m-d');
    $tomorrow = new DateTime('tomorrow');
    $tomorrow_string = $tomorrow->format('Y-m-d');

    foreach ($slides as $slide) {
      $options = $slide->getOptions();

      // Each opening-hours slide has a feed-parameter containing 0-2 feeds
      // 0 if it is not configured, 1 library, and 2 if the library has 
      // citizenservices attached.
      if (empty($options['feed']) || !is_array($options['feed'])) {
        continue;
      }

      // Contains the "<from> - <to>" intervals found while parsing the
      // feed. Keyed by library/citizenservices and for libraries sub-keyed
      // by open/service.
      // NULL means we don't have any data for the service, ie. it should
      // not be shown. If we have data for the service but the service is
      // closed we'll put an explicit "closed" as value.
      $intervals = [
        'library' => [
          'open' => NULL,
          'service' => NULL,
        ],
        'citizenservices' => [
          'open' => NULL,
        ],
      ];

      foreach ($options['feed'] as $key => $nid)  {
        // Only attempt to process the configuration if it's actually there.
        if (empty($options['feed'][$key])) {
          continue;
        }
        
        // Build up the full feed URL.
        $replacements = [
          '%from%' => $today_string,
          '%to%' => $tomorrow_string,
          '%nid%' => $nid
        ];
        $url = $this->dingUrl . str_replace(array_keys($replacements), array_values($replacements), self::OPENING_HOURS_FEED_PATH);

        list($json, $error) = $this->retriveFeed($url);
        if ($error) {
          continue;
        }
        $data = \json_decode($json, TRUE);
        if (JSON_ERROR_NONE !== json_last_error()) {
          $this->logger->error('json_decode error: ' . json_last_error_msg());
            continue;
        }
        if (is_array(!$data)) {
          $this->logger->error('Could not decode the feed: ' . print_r($data, TRUE));
          continue;
        }

        // Handle libraries.
        if ($key === 'library') {
          // Default opening-status.
          $intervals['library']['open'] = 'closed';
          if (count($data) > 1) {
            // Libraries can have multiple intervals. In that case the
            // interval we want will be tagged with a specific notice.
            // We're looking for intervals for the main opening and
            // service.
            foreach ($data as $interval_candidate) {
              // We need a notice text to work with, and the interval has to
              // be todays interval.
              if (empty($interval_candidate['notice']) || $interval_candidate['date'] !== $today_string) {
                continue;
              }

              // Match against (locale-neutral) lowercase.
              $lowercase_notice = mb_convert_case(
                $interval_candidate['notice'],
                MB_CASE_LOWER,
                'UTF-8'
              );

              if (in_array($lowercase_notice, $this->open_notice_values, FALSE)) {
                $intervals['library']['open'] = "{$interval_candidate['start_time']} - {$interval_candidate['end_time']}";
              }

              if (in_array($lowercase_notice, $this->service_notice_values, FALSE)) {
                $intervals['library']['service'] = "{$interval_candidate['start_time']} - {$interval_candidate['end_time']}";
              }
            }
          }
          // If we only have the one interval and it does not have a notice
          // we'll accept it as main opening hours, and skip setting
          // service-opening-hours.
          else if (count($data) === 1 && empty($data[0]['notice'])){
            $interval = reset($data);
            if ($interval['date'] === $today_string) {
              $intervals['library']['open'] = "{$interval['start_time']} - {$interval['end_time']}";
            }
          }
        }

        // Handle citizenservices, we ignore notices on the intervals for now
        // and will be satisfied with anything we find.
        if ($key === 'citizenservices') {
          // Default if we don't find some data below.
          $intervals['citizenservices']['open'] = 'closed';

          // Look for todays date (in case we got several intervals).
          foreach ($data as $interval) {
            if (!empty($interval['date']) && $interval['date'] === $today_string) {
              $intervals['citizenservices']['open'] = "{$interval['start_time']} - {$interval['end_time']}";
            }
          }
        }
      }
      // Generate texts for the intervals.
      $interval_texts = $this->generateTexts($intervals);
      $date_headline = strftime(self::HUMAN_DATE_FORMAT_FULL, $today->getTimestamp());
      // Prepare the texts for openingHoursSlide.js to pick up.
      $slide->setExternalData(['intervalTexts' => $interval_texts, 'date_headline' => $date_headline]);
      $this->entityManager->flush();

    }
  }

  /**
   * Fetches the feed and return its content and an error-object.
   */
  private function retriveFeed($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    //return the transfer as a string
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FAILONERROR, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
    $this->logger->info("Ding2Service: Fetching feed at $url");
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if (!empty($error)) {
      $this->logger->error(
        'Ding2Service: Error while fetching feed ' . $url . ' : ' . $error
      );
    }


    return array($response, $error);
  }

  /**
   * Generates texts to be displayed on a slide.
   *
   * @param $intervals
   *   Array containing intervals for citizenservices and a library.
   */
  private function generateTexts($intervals) {
    $texts = [
      'library' => [
        'open' => NULL,
        'service' => NULL,
      ],
      'citizenservices' => [
        'open' => NULL,
      ],
    ];


    // Show openinghours for the library if we have a value for it. If the
    // library is open, we also show a self-service interval.
    if (isset($intervals['library']['open'])) {
      if ($intervals['library']['open'] === 'closed') {
        $texts['library']['open'] =  "Biblioteket er lukket i dag";
      } else {

        $texts['library']['open'] = "Biblioteket har i dag åbent kl. {$intervals['library']['open']}";

        // Show specific service-hours if present. If not, just put out a note
        // that the library is self-serviced.
        if (isset($intervals['library']['service'])) {
          $texts['library']['service'] = "og der er betjening kl. {$intervals['library']['service']}";
        } else {
          $texts['library']['service'] =  "og der er selvbetjening i hele åbningstiden";
        }
      }
    }

    // Show opening-hours for Citizen Services.
    if (isset($intervals['citizenservices']['open'])) {
      // If the interval is non-null, it means the library has Citizen
      // Services, but it may still be closed.
      if ($intervals['citizenservices']['open'] === 'closed') {
        $texts['citizenservices']['open'] = "I dag har Borgerservice lukket";
      }
      else {
        $texts['citizenservices']['open'] = "Borgerservice har åbent kl. {$intervals['citizenservices']['open']}";
      }
    }
     return $texts;
  }

  /**
   * Finds all ding-event slides and updates their external-data.
   */
  protected function updateDingEvents() {
    // Get our slides and go trough each looking for feeds to import.
    // Most other services finds their slides by type, but as we piggyback a
    // lot on the rss-type and thus have to have "rss" as slide-type for ding-
    // events, we instead search by template.
    /** @var Slide[] $slides */
    $slides = $this->slideRepo->findByTemplate('ding-events');
    foreach ($slides as $slide) {
      $options = $slide->getOptions();

      // Fetch feed by library-slug.
      if (empty($options['slug'])) {
        continue;
      }
      $slug = $options['slug'];
      // Build up the full feed URL.
      $replacements = [
        '%slug%' => $slug,
      ];
      $url = $this->dingUrl . str_replace(
          array_keys($replacements),
          array_values($replacements),
          self::DING_EVENTS_FEED_PATH
        );
      list($response, $error) = $this->retriveFeed($url);
      if ($error) {
        // Error has been logged, so just continue.
        continue;
      }

      // Parse XML.
      libxml_use_internal_errors(true);
      $xml = simplexml_load_string($response);
      if ($xml === FALSE) {
        echo "Failed loading XML\n";
        $errors = [];
        foreach(libxml_get_errors() as $error) {
          $errors[] = $error->message;
        }
        $this->logger->error("Could not parse feed: \n" . implode("\n", $errors));
        continue;
      }

      // Find all events in the feed and add them to a list we're going to pass
      // on to the slide.
      $events = [];
      if (empty($xml->activity)) {
        $this->logger->error("The activity feed was empty ($url)");
        continue;
      }

      foreach ($xml->activity as $activity) {
        // Get the fields we want, SimpleXML will return null-objects if the
        // elements does not exist, so the following is safe without prechecks.
        $event['start'] = (string) $activity->startdato;
        $event['end'] = (string) $activity->slutdato;
        $event['title'] = (string) $activity->titel;
        $event['list_image'] = (string) $activity->list_image;
        $event['description'] = (string) $activity->beskrivelse;

        // Determine if we have enough data to continue.
        if (empty($event['start']) || empty($event['end']) || empty($event['title'])) {
          $uid = empty((string)$activity->uid) ? "(unknown)" : (string)$activity->uid;
          $this->logger->notice("Not enough info for activity with uid $uid, (title/start/end) = ({$event['title']}/{$event['start']}/{$event['end']}) skipping");
        }

        // Format time-intervals to be presentable.
        $start_datetime = new DateTime($event['start']);
        $event['start_time'] = $start_datetime->format('H:i');
        $end_datetime = new DateTime($event['end']);
        $event['end_time'] = $end_datetime->format('H:i');

        // Skip events in the past.
        if ($start_datetime->getTimestamp() < $this->today->getTimestamp()) {
          continue;
        }

        // Add date and time-stamps for the organization-code in
        // openingHoursSlide.js to work with.
        $date = $start_datetime->format('Y-m-d');
        $event['date'] = $date;
        $event['timestamp'] = $start_datetime->getTimestamp();

        // Prepare headlines for the date-groupings.
        $date_headline = strftime(
          self::HUMAN_DATE_FORMAT_FULL,
          $start_datetime->getTimestamp()
        );

        if ($date === $this->today_formatted) {
          $date_headline = 'I dag, ' . $date_headline;
        }

        if ($date === $this->tomorrow_formatted) {
          $date_headline = 'I morgen, ' . $date_headline;
        }
        $event['date_headline'] = $date_headline;

        // Add the event to the list, keyed by its start-time for easier
        // sorting.
        $events[$start_datetime->getTimestamp()] = $event;
      }


      // We now have all events, make sure they are sorted from earliest to
      // latest.
      ksort($events);

      // Package the data up for openingHoursSlide.js
      $external_data =
        [
          // We need this to be an array when we hit js for easier iteration.
          'events' => array_values($events),
        ];
      $slide->setExternalData($external_data);
      // Make sure the data is written to db.
      $this->entityManager->flush();
    }
  }
}
