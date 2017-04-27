<?php
/**
 * @file
 * Contains the user manager.
 */

namespace Indholdskanalen\MainBundle\Services;

use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class UserManager
 * @package Indholdskanalen\MainBundle\Services
 */
class UserManager {
  protected $userManager;
  protected $mailerService;

  /**
   * UserManager constructor.
   *
   * @param \FOS\UserBundle\Doctrine\UserManager $userManager
   * @param \Indholdskanalen\MainBundle\Services\UserMailerService $mailerService
   */
  public function __construct(FOSUserManager $userManager, UserMailerService $mailerService) {
    $this->userManager = $userManager;
    $this->mailerService = $mailerService;
  }

  /**
   * Create a user.
   *
   * @param $email
   * @param $firstname
   * @param $lastname
   * @return \FOS\UserBundle\Model\UserInterface
   * @throws HttpException
   */
  public function createUser($email, $firstname, $lastname) {
    $user = $this->userManager->findUserByEmail($email);
    if ($user) {
      throw new HttpException(409, 'User already exists');
    }

    // Create user object.
    $user = $this->userManager->createUser();
    $user->setUsername($email);
    $user->setEmail($email);
    $user->setPlainPassword(uniqid());
    $user->setFirstname($firstname);
    $user->setLastname($lastname);
    $user->setEnabled(TRUE);

    // Send confirmation email.
    if (null === $user->getConfirmationToken()) {
      /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
      $tokenGenerator = $this->container->get('fos_user.util.token_generator');
      $user->setConfirmationToken($tokenGenerator->generateToken());
    }
    $this->mailerService->sendUserCreatedEmailMessage($user);
    $user->setPasswordRequestedAt(new \DateTime());

    $this->userManager->updateUser($user);

    return $user;
  }
}
