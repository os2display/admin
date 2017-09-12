<?php
/**
 * @file
 * Contains CronEvent.
 */

namespace Os2Display\CoreBundle\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CronEvent
 * @package Os2Display\CoreBundle\Events
 */
class CronEvent extends Event {
  const EVENT_NAME = 'ik.cron';
}
