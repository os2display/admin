<?php
/**
 * @file
 * Contains the user manager.
 */

namespace Indholdskanalen\MainBundle\Services;

use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Exception\DuplicateEntityException;

/**
 * Class UserManager
 * @package Indholdskanalen\MainBundle\Services
 */
class UserManager {
  protected static $editableProperties = ['email', 'firstname', 'lastname', 'roles'];

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

    $this->entityService->setValues($user, $data, self::$editableProperties);

    $user->setUsername($user->getEmail());
    $user->setPlainPassword(uniqid());
    $user->setEnabled(TRUE);

    $this->entityService->validateEntity($user);

    if ($this->userManager->findUserByEmail($user->getEmail())) {
      throw new DuplicateEntityException('User already exists.', $data);
    }

    // Send confirmation email.
    if (NULL === $user->getConfirmationToken()) {
      $user->setConfirmationToken($this->tokenGenerator->generateToken());
    }
    $this->mailerService->sendUserCreatedEmailMessage($user);
    $user->setPasswordRequestedAt(new \DateTime());

    $this->userManager->updateUser($user);

    return $user;
  }

  /**
   * Update a user.
   *
   * @param \Indholdskanalen\MainBundle\Services\User $user
   * @param $data
   * @return \FOS\UserBundle\Model\UserInterface
   * @throws \Indholdskanalen\MainBundle\Exception\DuplicateEntityException
   */
  public function updateUser(User $user, $data) {
    $this->entityService->setValues($user, $data, self::$editableProperties);

    $user->setUsername($user->getEmail());

    $this->entityService->validateEntity($user);

    $anotherUser = $this->userManager->findUserByEmail($user->getEmail());
    if ($anotherUser && $anotherUser->getId() !== $user->getId()) {
      throw new DuplicateEntityException('User already exists.', $data);
    }

    $this->userManager->updateUser($user);

    return $user;
  }

}
