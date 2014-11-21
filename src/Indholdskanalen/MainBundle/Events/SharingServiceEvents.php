<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Events;

/**
 * Class SharingServiceEvents
 */
final class SharingServiceEvents {
  const ADD_CHANNEL_TO_INDEX = 'indholdskanalen_sharing_service.add_channel_to_index';
  const REMOVE_CHANNEL_FROM_INDEX = 'indholdskanalen_sharing_service.remove_channel_from_index';
  const UPDATE_CHANNEL = 'indholdskanalen_sharing_service.update_channel';
}
