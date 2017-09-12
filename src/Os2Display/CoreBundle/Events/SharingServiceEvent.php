<?php
/**
 * @file
 * This file is a part of the Os2Display CoreBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Os2Display\CoreBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use Os2Display\CoreBundle\Entity\Channel;
use Os2Display\CoreBundle\Entity\SharingIndex;

/**
 * Class SharingServiceEvent
 * @package Os2Display\CoreBundle\Events
 */
class SharingServiceEvent extends Event {
  protected $channel;
  protected $sharingIndex;

  /**
   * Constructor
   *
   * @param Channel $channel
   * @param SharingIndex $sharingIndex
   */
  public function __construct(Channel $channel, SharingIndex $sharingIndex) {
    $this->channel = $channel;
    $this->sharingIndex = $sharingIndex;
  }

  /**
   * @return Channel
   */
  public function getChannel() {
    return $this->channel;
  }

  /**
   * @return SharingIndex
   */
  public function getSharingIndex() {
    return $this->sharingIndex;
  }
}
