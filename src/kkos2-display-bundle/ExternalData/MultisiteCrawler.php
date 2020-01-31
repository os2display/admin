<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MultisiteCrawler
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\ExternalData
 */
class MultisiteCrawler {

  /**
   * Get the value of an attribute from a CSS selector.
   *
   * @param string $html HTML to search for images in
   * @param string $selector A CSS selector
   * @param string $attribute An HTML element attribute
   *
   * @return array
   */
  public function getAttributeValues($html, $selector, $attribute) {

    $crawler = new Crawler($html);

    $urls = $crawler->filter($selector)
      ->each(function (Crawler $node, $i) use ($attribute) {
        return $node->attr($attribute) ?: '';
      });
    return array_filter($urls);

  }

  /**
   *
   * @param string $html HTML to search for images in
   * @param string $selector A CSS selector
   *
   * @return array
   */
  public function getImageUrls($html, $selector) {
    return $this->getAttributeValues($html, $selector, 'src');
  }

}
