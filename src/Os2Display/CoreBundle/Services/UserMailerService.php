<?php
/**
 * @file
 * Contains the user mailer service.
 */

namespace Os2Display\CoreBundle\Services;

use FOS\UserBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class UserMailerService
 * @package Os2Display\CoreBundle\Services
 */
class UserMailerService extends Mailer {
  /**
   * Send user created mail to user.
   *
   * @param UserInterface $user
   */
  public function sendUserCreatedEmailMessage(UserInterface $user) {
    $rendered = $this->templating->render(
      'Os2DisplayCoreBundle:User:mailer.html.twig', [
        'name' => $user->getFirstname(),
        'url' => $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), TRUE),
      ]
    );

    $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $user->getEmail());
  }
}
