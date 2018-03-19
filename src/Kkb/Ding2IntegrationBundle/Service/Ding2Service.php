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

  /**
   * Map of [term id => categoryname]
   *
   * Maps an opening hours category name ("citizenservices", "libraryservice")
   * to its corresponding DDB CMS Opening Hours category taxonomy term.
   *
   * @var array
   */
  private $openingHoursCategories = [];

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

    // Get categories for opening hours.
    if ($this->container->hasParameter('ding_opening_hours_category') && is_array($this->container->getParameter('ding_opening_hours_category'))) {
      // Configuration is a map with categories as key and term id's as value.
      // For lookup we need a map of tids pointing to categories.
      $configured_categories = $this->container->getParameter('ding_opening_hours_category');
      array_walk($configured_categories, function($tid, $category) {
        // In some future version of the integration we might be tad more 
        // dynamic in handling the types of categories, but for now we go with
        // robustness as we know the two possible categories.
        if (is_numeric($tid) && in_array($category, ['libraryservice', 'citizenservices'], TRUE)) {
          $this->openingHoursCategories[$tid] = $category;
        }
      });
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

      // Get the Drupal Node ID for the library. It is required so continue if
      // it is missing.
      if (!isset($options['feed']['library'])) {
        continue;
      }
      $nid = $options['feed']['library'];

      // Each opening-hours slide has a feed-parameter containing 0-2 feeds
      // 0 if it is not configured, 1 library, and 2 if the library has
      // citizenservices attached.
      if (empty($options['feed']) || !is_array($options['feed'])) {
        continue;
      }

      // Temporary migration fix, openinghours for citizenservices had their
      // own nid. Going forward that data is now contained in the libraries
      // feed. So, we now have a toggle to indicate whether to process
      // citizenservices openinghours for this library.
      // In the interim period we'll port any "citizenservices" setting over
      // into a new citizenservicesenabled setting.
      if (isset($options['feed']['citizenservices'])) {
        // Toggle citizenservicesenabled
        $options['feed']['citizenservicesenabled'] = TRUE;
        // We no longer need the old settings, remove it and update the slide.
        unset($options['feed']['citizenservices']);
        $slide->setOptions($options);
      }

      // Build up the full feed URL we're going to pull data from.
      $replacements = [
        '%from%' => $today_string,
        '%to%' => $tomorrow_string,
        '%nid%' => $nid
      ];
      $url = $this->dingUrl . str_replace(array_keys($replacements), array_values($replacements), self::OPENING_HOURS_FEED_PATH);

      // Get the data from the feed, error out if we run in to trouble.
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

      // Build up the intervals array we're going to pass to the screen.
      // The interval consists of up to 3 entries for "general" (required),
      // "service" (optional) and "citizenservices" (required if enabled).
      $intervals = [];

      // If we can't find the general interval, set everything as closed.
      $intervals['general'] = 'closed';

      // If citizenservices is enabled, we'll always show the interval, so we
      // need to default to closed as well.
      $citizenservices_enabled = !empty($options['feed']['citizenservicesenabled']);
      if ($citizenservices_enabled) {
        $intervals['citizenservices'] = 'closed';
      }

      // Process the feed, pick up the intervals we need for the screen.
      foreach ($data as $interval) {
        // Only process intervals for today
        if ($interval['date'] !== $today_string) {
          continue;
        }

        // Determine what category this interval belongs to. We pick the
        // category based a taxonomy term id. If it is null we assume it is a
        // general interval so we use that category as a default.
        $category = 'general';
        // Detect the category.
        if (isset($interval['category_tid']) && is_numeric($interval['category_tid'])) {
          if (isset($this->openingHoursCategories[$interval['category_tid']])) {
            $category = $this->openingHoursCategories[$interval['category_tid']];
          } else {
            // Unknown category, skip.
            continue;
          }
        }

        // Skip citizenservices if it is not enabled.
        if (!$citizenservices_enabled && $category === 'citizenservices') {
          continue;
        }

        $intervals[$category] = "{$interval['start_time']} - {$interval['end_time']}";
      }

      // Generate texts for the intervals and store it into the slides external-
      // data property for openingHoursSlide.js to pick up.
      $interval_texts = $this->generateTexts($intervals);
      $date_headline = strftime(self::HUMAN_DATE_FORMAT_FULL, $today->getTimestamp());
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
      'libraryservice' => NULL,
      'citizenservices' => NULL,
      'general' => NULL,
    ];

    // Show openinghours for the library if we have a value for it. If the
    // library is open, we also show a self-service interval.
    if (isset($intervals['general'])) {
      if ($intervals['general'] === 'closed') {
        $texts['general'] =  "Biblioteket er lukket i dag";
      } else {
        $texts['general'] = "Biblioteket har i dag åbent kl. {$intervals['general']}";

        // Show specific service-hours if present. If not, just put out a note
        // that the library is self-serviced.
        if (isset($intervals['libraryservice'])) {
          $texts['libraryservice'] = "og der er betjening kl. {$intervals['libraryservice']}";
        } else {
          $texts['libraryservice'] =  "og der er selvbetjening i hele åbningstiden";
        }
      }
    }

    // Show opening-hours for Citizen Services.
    if (isset($intervals['citizenservices'])) {
      // If the interval is non-null, it means the library has Citizen
      // Services, but it may still be closed.
      if ($intervals['citizenservices'] === 'closed') {
        $texts['citizenservices'] = "I dag har Borgerservice lukket";
      }
      else {
        $texts['citizenservices'] = "Borgerservice har åbent kl. {$intervals['citizenservices']}";
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
        $event['start'] = $this->prepareDate((string) $activity->startdato);
        $event['end'] = $this->prepareDate((string) $activity->slutdato);
        $event['title'] = (string) $activity->titel;
        $event['list_image'] = (string) $activity->list_image;
        $event['description'] = (string) $activity->beskrivelse;
        $event['all_day'] = stripos((string) $activity->startdato, 'Hele dagen') !== FALSE;
        $uid = empty((string)$activity->uid) ? "(unknown)" : (string)$activity->uid;

        // Make sure we could parse dates, if not skip the activity.
        foreach ([
                   ['start', (string) $activity->startdato, $event['start']],
                   ['end', (string) $activity->slutdato, $event['end']],
                 ] as list($field, $org, $parsed)) {
          if ($parsed === NULL) {
            $this->logger->error("Unable to parse {$field}-time value '$org' - skipping activity with (uid/title) ('$uid'/'{$event['title']}')");
            // We're already inside a foreach.
            continue(2);

          }
        }
        // Determine if we have enough data to continue.
        if (empty($event['start']) || empty($event['end']) || empty($event['title'])) {
          $this->logger->error("Not enough info for activity with uid $uid, (title/start/end) = ('{$event['title']}'/'{$event['start']}'/'{$event['end']}') skipping");
          continue;
        }

        // Format time-intervals to be presentable.
        try {
          $start_datetime = new DateTime($event['start']);
          $event['start_time'] = $start_datetime->format('H:i');
          $end_datetime = new DateTime($event['end']);
          $event['end_time'] = $end_datetime->format('H:i');
        } catch (\Exception $e) {
          $this->logger->error("Error while parsing start/end for uid $uid, (title/start/end) = ('{$event['title']}'/'{$event['start']}'/'{$event['end']}') skipping activity. Message: " . $e->getMessage());
          continue;
        }

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

  /**
   * Basic cleanup of a date before we pass it to DateTime.
   *
   * @param string $date_string
   *   The date
   *
   * @return string|NULL
   *   The cleaned string, or NULL if we could not parse it.
   */
  private function prepareDate($date_string) {
    // The cleanup is based on knowledge of how the ding_kultunaut_feed module
    // used on bibliotek.kk.dk formats its dates.

    // Dates will either be formatted as an all-day event:
    // 2017/10/17 (Hele dagen).

    // Or with time-granularity:
    // 2017/10/26 09:30:00 CEST.

    // Match all day pattern.
    if (preg_match('#^(\d{4}/\d{1,2}/\d{1,2}).*Hele dagen#i', $date_string, $matches)) {
      // Pick out the date we expect to be the start of the string.
      return $matches[1];
    }

    // Match with time-granularity - in this case we don't do match to verify
    // the time-zone. We just want something that looks roughly like a date and
    // a time.
    if (preg_match('#^\d{4}/\d{1,2}/\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}#i', $date_string)) {
      // Pick out the date we expect to be the start of the string.
      return $date_string;
    }

    // No match, bail out.
    return NULL;
  }
}
