<?php
/**
 * @file
 * Contains the user manager.
 */

namespace Indholdskanalen\MainBundle\Services;

use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Indholdskanalen\MainBundle\Exception\DuplicateEntityException;

/**
 * Class UserManager
 * @package Indholdskanalen\MainBundle\Services
 */
class UserManager {
  protected $userManager;
  protected $mailerService;
  protected $entityService;
  protected $tokenGenerator;

  /**
   * UserManager constructor.
   *
   * @param \FOS\UserBundle\Doctrine\UserManager $userManager
   * @param \Indholdskanalen\MainBundle\Services\UserMailerService $mailerService
   */
  public function __construct(FOSUserManager $userManager, UserMailerService $mailerService, EntityService $entityService, TokenGeneratorInterface $tokenGenerator) {
    $this->userManager = $userManager;
    $this->mailerService = $mailerService;
    $this->entityService = $entityService;
    $this->tokenGenerator = $tokenGenerator;
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

    if ($this->userManager->findUserByEmail($user->getEmail())) {
      throw new DuplicateEntityException('User already exists.', $data);
    }

    // Send confirmation email.
    if (null === $user->getConfirmationToken()) {
      $user->setConfirmationToken($this->tokenGenerator->generateToken());
    }
    $this->mailerService->sendUserCreatedEmailMessage($user);
    $user->setPasswordRequestedAt(new \DateTime());

    $this->userManager->updateUser($user);

    return $user;
  }
}
