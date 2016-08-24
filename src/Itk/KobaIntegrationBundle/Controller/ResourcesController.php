<?php

namespace Itk\KobaIntegrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/koba/resources")
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
    return new JsonResponse($this->get('itk.koba_service')->getResources());
  }

  /**
   * The bookings of a resource.
   *
   * @Route("/{resourceMail}/bookings/from/{from}/to/{to}")
   *
   * @param $resourceMail
   *
   * @return JsonResponse
   */
  public function getResourceBookings($resourceMail, $from, $to) {
    return new JsonResponse($this->get('itk.koba_service')->getResourceBookings($resourceMail, 'default', $from, $to));
  }
}
