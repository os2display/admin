<?php
/**
 * @file
 * Contains user controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use Indholdskanalen\MainBundle\CustomJsonResponse;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\UserGroup;

/**
 * @Route("/api/user_group")
 */
class UserGroupController extends Controller {
  /**
   * Deletes a user group entity.
   *
   * @Route("/{id}", name="api_user_delete_group")
   * @Method("DELETE")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\UserGroup $userGroup
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function deleteGroupAction(Request $request, UserGroup $userGroup) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($userGroup);
    $em->flush();

    return new CustomJsonResponse(204);
  }
}
