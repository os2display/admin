<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/resources")
 */
class ResourcesController extends Controller {
  /**
   * Get available resources.
   *
   * @Route("")
   *
   * @return JsonResponse
   */
  public function getResources() {
    return new JsonResponse($this->get('indholdskanalen.koba_service')->getResources());
  }

  /**
   * The bookings of a resource.
   *
   * @Route("/{resourceMail}")
   *
   * @param $resourceMail
   *
   * @return JsonResponse
   */
  public function getBookingsForResource($resourceMail) {
    return new JsonResponse($this->get('indholdskanalen.koba_service')->getBookingsForResource($resourceMail));
  }
}
