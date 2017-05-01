<?php
/**
 * @file
 * Contains user controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Indholdskanalen\MainBundle\Entity\UserGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/user_group")
 * @Rest\View(serializerGroups={"api"})
 */
class UserGroupController extends FOSRestController {
  /**
   * @Rest\Get("/", name="api_user_group_list")
   * @return \FOS\RestBundle\View\View
   */
  public function getUserGroupsAction() {
    $em = $this->getDoctrine()->getManager();
    $userGroups = $em->getRepository(UserGroup::class)->findAll();

    return $userGroups;
  }

  /**
   * Deletes a user group entity.
   *
   * @Rest\Delete("/{id}", name="api_user_delete_group")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\UserGroup $userGroup
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function deleteGroupAction(Request $request, UserGroup $userGroup) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($userGroup);
    $em->flush();

    return $this->view(null, Codes::HTTP_NO_CONTENT);
  }
}
