<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

/**
 * Class MultisiteHelper
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\ExternalData
 */
class MultisiteHelper {

  /**
   * Try to get the original image url from an image style preset on a multisite
   *
   * Because we know that the multisites run on Drupal we can make an educated
   * guess on the iamge url.
   *
   * @param string $imgUrl The url to the image style preset image
   *
   * @return string
   */
  public function getOriginalImagePath($imgUrl) {
    if (!strpos($imgUrl, '/files/styles/')) {
      return $imgUrl;
    }
    return preg_replace("@/sites/.*/files/styles/.*/public@", '/files', $imgUrl);
  }

}
