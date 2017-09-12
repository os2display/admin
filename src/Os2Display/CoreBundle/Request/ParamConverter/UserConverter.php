<?php

namespace Os2Display\CoreBundle\Request\ParamConverter;

use Os2Display\CoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UserConverter
 *
 * A parameter converter that can handle "current" as user id and returns the
 * currently authententicated user.
 *
 * @package Os2Display\CoreBundle\Request\ParamConverter
 */
class UserConverter extends DoctrineParamConverter {
  protected $tokenStorage;

  public function __construct(ManagerRegistry $registry = NULL, TokenStorageInterface $tokenStorage) {
    parent::__construct($registry);
    $this->tokenStorage = $tokenStorage;
  }

  public function supports(ParamConverter $configuration) {
    return parent::supports($configuration) && $configuration->getClass() === User::class;
  }

  protected function getIdentifier(Request $request, $options, $name) {
    $id = parent::getIdentifier($request, $options, $name);

    if ($id === 'current') {
      $token = $this->tokenStorage->getToken();
      if ($token && $token->getUser()) {
        $id = $token->getUser()->getId();
      }
    }

    return $id;
  }

}
