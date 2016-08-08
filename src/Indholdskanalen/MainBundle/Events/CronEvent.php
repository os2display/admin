<?php
/**
 * @file
 * Contains CronEvent.
 */

namespace Indholdskanalen\MainBundle\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CronEvent
 * @package Indholdskanalen\MainBundle\Events
 */
class CronEvent extends Event {
  const EVENT_NAME = 'ik.cron';
}