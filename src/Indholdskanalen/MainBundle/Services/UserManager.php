<?php
/**
 * @file
 * Contains the user manager.
 */

namespace Indholdskanalen\MainBundle\Services;

use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Indholdskanalen\MainBundle\Exception\DuplicateEntityException;

/**
 * Class UserManager
 * @package Indholdskanalen\MainBundle\Services
 */
class UserManager {
  protected $userManager;
  protected $mailerService;
  protected $entityService;

  /**
   * UserManager constructor.
   *
   * @param \FOS\UserBundle\Doctrine\UserManager $userManager
   * @param \Indholdskanalen\MainBundle\Services\UserMailerService $mailerService
   */
  public function __construct(FOSUserManager $userManager, UserMailerService $mailerService, EntityService $entityService) {
    $this->userManager = $userManager;
    $this->mailerService = $mailerService;
    $this->entityService = $entityService;
  }

  /**
   * Create a user.
   *
   * @param $data
   * @return \FOS\UserBundle\Model\UserInterface
   * @throws \Indholdskanalen\MainBundle\Exception\DuplicateEntityException
   */
  public function createUser($data) {
    // Create user object.
    $user = $this->userManager->createUser();

    $properties = ['email', 'firstname', 'lastname'];

    $this->entityService->setValues($user, $data, $properties);

    $user->setUsername($user->getEmail());
    $user->setPlainPassword(uniqid());
    $user->setEnabled(TRUE);

    $this->entityService->validateEntity($user);

    $user = $this->userManager->findUserByEmail($user->getEmail());
    if ($user) {
      throw new DuplicateEntityException('User already exists', $user);
    }

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
