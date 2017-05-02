<?php
/**
 * @file
 * Contains user controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\UserGroup;

/**
 * @Route("/api/user_group")
 */
class UserGroupController extends ApiController {
  /**
   * Deletes a user group entity.
   *
   * @Route("/{id}", name="api_user_delete_group")
   * @Method("DELETE")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\UserGroup $userGroup
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function deleteGroupAction(Request $request, UserGroup $userGroup) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($userGroup);
    $em->flush();

    return new Response(null, 204);
  }
}
