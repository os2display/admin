<?php
/**
 * @file
 * Contains the user mailer service.
 */

namespace Indholdskanalen\MainBundle\Services;

use FOS\UserBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class UserMailerService
 * @package Indholdskanalen\MainBundle\Services
 */
class UserMailerService extends Mailer {
  /**
   * Send user created mail to user.
   *
   * @param UserInterface $user
   */
  public function sendUserCreatedEmailMessage(UserInterface $user) {
    $rendered = $this->templating->render(
      'IndholdskanalenMainBundle:User:mailer.html.twig', [
        'name' => $user->getFirstname(),
        'url' => $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), TRUE),
      ]
    );

    $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $user->getEmail());
  }
}
