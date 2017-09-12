<?php

namespace Os2Display\CoreBundle\Services;

use Os2Display\CoreBundle\Entity\Channel;
use Os2Display\CoreBundle\Entity\Group;
use Os2Display\CoreBundle\Entity\GroupableEntity;
use Os2Display\CoreBundle\Entity\Screen;
use Os2Display\CoreBundle\Entity\Slide;
use Os2Display\CoreBundle\Entity\User;
use Os2Display\CoreBundle\Security\EditVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Role\Role;

class ApiDataService {
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * Set API data on an object or a list of objects.
   *
   * @param $object
   * @return mixed
   */
  public function setApiData($object, $inCollection = FALSE) {
    if (is_array($object)) {
      foreach ($object as $item) {
        $this->setApiData($item, TRUE);
      }
    }
    elseif ($object instanceof Group) {
      $this->setApiDataGroup($object, $inCollection);
    }
    elseif ($object instanceof User) {
      $this->setApiDataUser($object, $inCollection);
    }
    elseif ($object instanceof GroupableEntity) {
      $this->setApiDataGroupable($object, $inCollection);
    }
    return $object;
  }

  protected function setApiDataGroupable(GroupableEntity $groupable, $inCollection = FALSE) {
    $securityMananger = $this->container->get('os2display.security_manager');

    $groupable->setApiData([
      'permissions' => [
        'can_read' => $securityMananger->decide(EditVoter::READ, $groupable),
        'can_update' => $securityMananger->decide(EditVoter::UPDATE, $groupable),
        'can_delete' => $securityMananger->decide(EditVoter::DELETE, $groupable),
      ]
    ]);

  }

  protected function setApiDataGroup(Group $group) {
    $securityMananger = $this->container->get('os2display.security_manager');

    $group->setApiData([
      'permissions' => [
        'can_read' => $securityMananger->decide(EditVoter::READ, $group),
        'can_update' => $securityMananger->decide(EditVoter::UPDATE, $group),
        'can_delete' => $securityMananger->decide(EditVoter::DELETE, $group),

        'can_add_user' => $securityMananger->decide('can_add_user', $group),
        'can_add_channel' => $securityMananger->decide('can_add_channel', $group),
        'can_add_slide' => $securityMananger->decide('can_add_slide', $group),
        'can_add_screen' => $securityMananger->decide('can_add_screen', $group),
      ]
    ]);
  }

  protected function setApiDataUser(User $user, $inCollection = FALSE) {
    $securityMananger = $this->container->get('os2display.security_manager');

    $permissions = [
      'can_read' => $securityMananger->decide(EditVoter::READ, $user),
      'can_update' => $securityMananger->decide(EditVoter::UPDATE, $user),
      'can_delete' => $securityMananger->decide(EditVoter::DELETE, $user),
    ];

    if (!$inCollection) {
      // Add permissions for current user.
      $token = $this->container->get('security.token_storage')->getToken();
      if ($token && $user == $token->getUser()) {
        $permissions += [
          'can_create_group' => $securityMananger->decide(EditVoter::CREATE, Group::class),
          'can_create_user' => $securityMananger->decide(EditVoter::CREATE, User::class),
          'can_create_channel' => $securityMananger->decide(EditVoter::CREATE, Channel::class),
          'can_create_slide' => $securityMananger->decide(EditVoter::CREATE, Slide::class),
          'can_create_screen' => $securityMananger->decide(EditVoter::CREATE, Screen::class),
        ];
      }
    }
    $userRoles = array_map(function ($role) {
      return new Role($role);
    }, $user->getRoles(FALSE));
    $roles = $this->container->get('security.role_hierarchy')->getReachableRoles($userRoles);
    $roles = array_unique(array_map(function (Role $role) { return $role->getRole(); }, $roles));

    $user->setApiData([
      'permissions' => $permissions,
      'roles' => $roles,
    ]);

    $translator = $this->container->get('translator');
    $request = $this->container->get('request_stack')->getCurrentRequest();
    $locale = $request->get('locale', $this->container->getParameter('locale'));

    $roleNames = [];
    foreach ($user->getRoles(FALSE, FALSE) as $roleName) {
      $roleNames[$roleName] = $translator->trans($roleName, [], 'Os2DisplayCoreBundle', $locale);
    }

    $user->setRoleNames($roleNames);
  }

}
