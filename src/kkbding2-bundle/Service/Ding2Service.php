<?php
/**
 * @file
 * Contains the DING2 service.
 *
 * Provides integration service with DING2.
 */

namespace Kkb\Ding2IntegrationBundle\Service;

use DateTime;
use Os2Display\CoreBundle\Entity\Slide;
use Os2Display\CoreBundle\Events\CronEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Din2Service
 */
class Ding2Service
{
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

    private $initialized = false;
    const OPENING_HOURS_FEED_PATH = '/opening_hours/instances?from_date=%from%&to_date=%to%&nid=%nid%';
    const DING_EVENTS_FEED_PATH = '/kultunaut_export/%slug%';
    // Eg. "fredag d. 9. december 2016"
    const HUMAN_DATE_FORMAT_FULL = '%A d. %e. %B %Y';

    // Array keys we use troughout handling of data from feeds.
    const KEY_CITIZENSERVICESENABLED = 'citizenservicesenabled';
    const KEY_INTERVAL_CITIZENSERVICES = 'citizenservices';
    const KEY_INTERVAL_LIBRARYSERVICE = 'libraryservice';
    const KEY_INTERVAL_GENERAL = 'general';

    protected $today;

    protected $todayFormatted;
    protected $tomorrow;
    protected $tomorrowFormatted;

    /**
     * Constructor for the ding 2 service.
     *
     * @param \Symfony\Component\DependencyInjection\Container $container
     *   Symfony container.
     *
     * @throws \Exception
     *   Thrown in case of errors while accesing the container.
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
          $this->tomorrow = new DateTime('tomorrow');
          $this->today = new DateTime('today');
          $this->tomorrowFormatted = $this->tomorrow->format('Y-m-d');
          $this->todayFormatted = $this->today->format('Y-m-d');

          $this->container = $container;
          $this->entityManager = $this->container->get('doctrine')->getManager();
          $this->slideRepo = $container->get('doctrine')->getRepository('Os2DisplayCoreBundle:Slide');
          $this->logger = $this->container->get('logger');

          // Setup the base url we're going to use to fetch the feeds.
        if ($this->container->hasParameter('ding_url')) {
            $this->dingUrl = $this->container->getParameter('ding_url');
            // We're unable to fetch anything if we don't have a valid Ding2 url.
            $this->initialized = true;
        }

        // Get categories for opening hours.
        if ($this->container->hasParameter('ding_opening_hours_category') && is_array($this->container->getParameter('ding_opening_hours_category'))) {
            // Configuration is a map with categories as key and term id's as value.
            // For lookup we need a map of tids pointing to categories.
            $configuredCategories = $this->container->getParameter('ding_opening_hours_category');
            array_walk($configuredCategories, function ($tid, $category) {
                // In some future version of the integration we might be tad more
                // dynamic in handling the types of categories, but for now we go with
                // robustness as we know the two possible categories.
                if (is_numeric($tid) && in_array($category, ['libraryservice', 'citizenservices'], true)) {
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
    public function onCron(CronEvent $event)
    {
        if ($this->initialized) {

            $oldLocale = setlocale(LC_TIME, 0);
            // Setup a time-stamp to display at the top of the screen. We want
            setlocale(LC_TIME, "da_DK.utf8");
            $this->updateOpeningHours();
            $this->updateDingEvents();
            setlocale(LC_TIME, $oldLocale);
        }
    }

    /**
     * Locate all opening-hours slides and download their feeds.
     */
    protected function updateOpeningHours()
    {
        /** @var Slide[] $slide */
        $slides = $this->slideRepo->findBySlideType('opening-hours');

        $today = new DateTime('today');
        $todayString = $today->format('Y-m-d');
        $tomorrow = new DateTime('tomorrow');
        $tomorrowString = $tomorrow->format('Y-m-d');

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
            if (isset($options['feed'][self::KEY_INTERVAL_CITIZENSERVICES])) {
                // Toggle citizenservicesenabled
                $options['feed'][self::KEY_CITIZENSERVICESENABLED] = true;
                // We no longer need the old settings, remove it and update the slide.
                unset($options['feed'][self::KEY_INTERVAL_CITIZENSERVICES]);
                $slide->setOptions($options);
            }

            // Build up the full feed URL we're going to pull data from.
            $replacements = [
                '%from%' => $todayString,
                '%to%' => $tomorrowString,
                '%nid%' => $nid,
            ];
            $url = $this->dingUrl.str_replace(array_keys($replacements), array_values($replacements), self::OPENING_HOURS_FEED_PATH);

            // Get the data from the feed, error out if we run in to trouble.
            list($json, $error) = $this->retriveFeed($url);
            if ($error) {
                continue;
            }
            $data = \json_decode($json, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                $this->logger->error('json_decode error: '.json_last_error_msg());
                continue;
            }
            if (is_array(!$data)) {
                $this->logger->error('Could not decode the feed: '.print_r($data, true));
                continue;
            }

            // Build up the intervals array we're going to pass to the screen.
            // The interval consists of up to 3 entries for "general" (required),
            // "service" (optional) and "citizenservices" (required if enabled).
            $intervals = [];

            // If we can't find the general interval, set everything as closed.
            $intervals[self::KEY_INTERVAL_GENERAL] = 'closed';
            $intervals[self::KEY_INTERVAL_LIBRARYSERVICE] = NULL;

            // If citizenservices is enabled, we'll always show the interval, so we
            // need to default to closed as well.
            $citizenServicesEnabled = !empty($options['feed'][self::KEY_CITIZENSERVICESENABLED]);
            if ($citizenServicesEnabled) {
                $intervals[self::KEY_INTERVAL_CITIZENSERVICES] = 'closed';
            }

            // Process the feed, pick up the intervals we need for the screen.
            foreach ($data as $interval) {
                // Only process intervals for today
                if ($interval['date'] !== $todayString) {
                    continue;
                }

                // Determine what category this interval belongs to. We pick the
                // category based a taxonomy term id. If it is null we assume it is a
                // general interval so we use that category as a default.
                $category = self::KEY_INTERVAL_GENERAL;
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
                if (!$citizenServicesEnabled && self::KEY_INTERVAL_CITIZENSERVICES === $category) {
                    continue;
                }

                $intervals[$category] = "{$interval['start_time']} - {$interval['end_time']}";
            }

            // Generate texts for the intervals and store it into the slides external-
            // data property for openingHoursSlide.js to pick up.
            $intervalTexts = $this->generateTexts($intervals);
            $dateHeadline = strftime(self::HUMAN_DATE_FORMAT_FULL, $today->getTimestamp());
            $slide->setExternalData(['intervalTexts' => $intervalTexts, 'date_headline' => $dateHeadline]);
            $this->entityManager->flush();
        }
    }

    /**
     * Finds all ding-event slides and updates their external-data.
     */
    protected function updateDingEvents()
    {
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
            $url = $this->dingUrl.str_replace(
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
            if (false === $xml) {
                echo "Failed loading XML\n";
                $errors = [];
                foreach (libxml_get_errors() as $error) {
                    $errors[] = $error->message;
                }
                $this->logger->error("Could not parse feed: \n".implode("\n", $errors));
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
                $event['all_day'] = stripos((string) $activity->startdato, 'Hele dagen') !== false;
                $uid = empty((string) $activity->uid) ? "(unknown)" : (string) $activity->uid;

                // Make sure we could parse dates, if not skip the activity.
                foreach ([
                             ['start', (string) $activity->startdato, $event['start']],
                             ['end', (string) $activity->slutdato, $event['end']],
                         ] as list($field, $org, $parsed)) {
                    if (null === $parsed) {
                        $this->logger->error(
                            "Unable to parse {$field}-time value '$org' - skipping activity with (uid/title) ('$uid'/'{$event['title']}')"
                        );
                        // We're already inside a foreach.
                        continue(2);
                    }
                }
                // Determine if we have enough data to continue.
                if (empty($event['start']) || empty($event['end']) || empty($event['title'])) {
                    $this->logger->error(
                        "Not enough info for activity with uid $uid, (title/start/end) = ('{$event['title']}'/'{$event['start']}'/'{$event['end']}') skipping"
                    );
                    continue;
                }

                // Format time-intervals to be presentable.
                try {
                    $startDateTime = new DateTime($event['start']);
                    $event['start_time'] = $startDateTime->format('H:i');
                    $endDateTime = new DateTime($event['end']);
                    $event['end_time'] = $endDateTime->format('H:i');
                } catch (\Exception $e) {
                    $this->logger->error(
                        "Error while parsing start/end for uid $uid, (title/start/end) = ('{$event['title']}'/'{$event['start']}'/'{$event['end']}') skipping activity. Message: ".$e->getMessage(
                        )
                    );
                    continue;
                }

                // Skip events in the past.
                if ($startDateTime->getTimestamp() < $this->today->getTimestamp()) {
                    continue;
                }

                // Add date and time-stamps for the organization-code in
                // openingHoursSlide.js to work with.
                $date = $startDateTime->format('Y-m-d');
                $event['date'] = $date;
                $event['timestamp'] = $startDateTime->getTimestamp();

                // Prepare headlines for the date-groupings.
                $dateHeadline = strftime(
                    self::HUMAN_DATE_FORMAT_FULL,
                    $startDateTime->getTimestamp()
                );

                if ($date === $this->todayFormatted) {
                    $dateHeadline = 'I dag, '.$dateHeadline;
                }

                if ($date === $this->tomorrowFormatted) {
                    $dateHeadline = 'I morgen, '.$dateHeadline;
                }
                $event['date_headline'] = $dateHeadline;

                // Add the event to the list, keyed by its start-time for easier
                // sorting.
                $events[$startDateTime->getTimestamp()] = $event;
            }


            // We now have all events, make sure they are sorted from earliest to
            // latest.
            ksort($events);

            // Package the data up for openingHoursSlide.js
            $externalData =
                [
                    // We need this to be an array when we hit js for easier iteration.
                    'events' => array_values($events),
                ];

          $slide->setExternalData($externalData);
            // Make sure the data is written to db.
            $this->entityManager->flush();
        }
    }

    /**
     * Fetches the feed and return its content and an error-object.
     *
     * @param string $url
     *   The url.
     * @return array
     *   An array containing the response and an error-object if an error occcured.
     */
    private function retriveFeed($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        //return the transfer as a string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        $this->logger->info("Ding2Service: Fetching feed at $url");
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if (!empty($error)) {
            $this->logger->error(
                'Ding2Service: Error while fetching feed '.$url.' : '.$error
            );
        }

        return array($response, $error);
    }

    /**
     * Generates texts to be displayed on a slide.
     *
     * @param array $intervals
     *   Array containing intervals for citizenservices and a library.
     *
     * @return array
     *   An array of interval texts matching the keys of the input array.
     */
    private function generateTexts($intervals)
    {
        $texts = [
            self::KEY_INTERVAL_LIBRARYSERVICE => null,
            self::KEY_INTERVAL_CITIZENSERVICES => null,
            self::KEY_INTERVAL_GENERAL => null,
        ];

        // Show openinghours for the library if we have a value for it. If the
        // library is open, we also show a self-service interval.
        if (isset($intervals[self::KEY_INTERVAL_GENERAL])) {
            if ($intervals[self::KEY_INTERVAL_GENERAL] === 'closed') {
                $texts[self::KEY_INTERVAL_GENERAL] = "Biblioteket er lukket i dag";
            } else {
                $texts[self::KEY_INTERVAL_GENERAL] = "Biblioteket har i dag åbent kl. {$intervals[self::KEY_INTERVAL_GENERAL]}";

                // Show specific service-hours if present. If not, just put out a note
                // that the library is self-serviced.
                if (isset($intervals[self::KEY_INTERVAL_LIBRARYSERVICE])) {
                    $texts[self::KEY_INTERVAL_LIBRARYSERVICE] = "og der er betjening kl. {$intervals[self::KEY_INTERVAL_LIBRARYSERVICE]}";
                } else {
                    $texts[self::KEY_INTERVAL_LIBRARYSERVICE] = "og der er selvbetjening i hele åbningstiden";
                }
            }
        }

        // Show opening-hours for Citizen Services.
        if (isset($intervals[self::KEY_INTERVAL_CITIZENSERVICES])) {
            // If the interval is non-null, it means the library has Citizen
            // Services, but it may still be closed.
            if ($intervals[self::KEY_INTERVAL_CITIZENSERVICES] === 'closed') {
                $texts[self::KEY_INTERVAL_CITIZENSERVICES] = "I dag har Borgerservice lukket";
            } else {
                $texts[self::KEY_INTERVAL_CITIZENSERVICES] = "Borgerservice har åbent kl. {$intervals[self::KEY_INTERVAL_CITIZENSERVICES]}";
            }
        }

        return $texts;
    }

    /**
     * Basic cleanup of a date before we pass it to DateTime.
     *
     * @param string $dateString
     *   The date
     *
     * @return string|NULL
     *   The cleaned string, or NULL if we could not parse it.
     */
    private function prepareDate($dateString)
    {
        // The cleanup is based on knowledge of how the ding_kultunaut_feed module
        // used on bibliotek.kk.dk formats its dates.

        // Dates will either be formatted as an all-day event:
        // 2017/10/17 (Hele dagen).

        // Or with time-granularity:
        // 2017/10/26 09:30:00 CEST.

        // Match all day pattern.
        if (preg_match('#^(\d{4}/\d{1,2}/\d{1,2}).*Hele dagen#i', $dateString, $matches)) {
            // Pick out the date we expect to be the start of the string.
            return $matches[1];
        }

        // Match with time-granularity - in this case we don't do match to verify
        // the time-zone. We just want something that looks roughly like a date and
        // a time.
        if (preg_match('#^\d{4}/\d{1,2}/\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}#i', $dateString)) {
            // Pick out the date we expect to be the start of the string.
            return $dateString;
        }

        // No match, bail out.
        return null;
    }
}
