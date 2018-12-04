<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Crawlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ServicespotCrawler.
 *
 * Gets data from KK multisite servicespots.
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\Crawlers
 */
class ServicespotCrawler
{
  /**
   * @var string $url
   */
  private $url;

  /**
   * @var Client $guzzler
   */
  private $guzzler;

  /**
   * @var array $content
   */
  private $content = [];

  /**
   * Construct.
   *
   * @param string $url
   *   Url to the KK multisite servicespot.
   */
  public function __construct($url)
  {
    $this->url = $url;
    $this->guzzler = new Client();
  }

  /**
   * Fetch and scrape the service spot.
   *
   * @throws \Exception
   *   If the url could not be fetched or parsed.
   */
  public function crawl()
  {
    try {
      $response = $this->guzzler->get($this->url);
      $body = $response->getBody();
      $html = (string) $body;
      $crawler = new Crawler($html);
      $crawler = $crawler->filter(('#main-content .node-service-spot'));
      $style = $crawler->attr('style');
      preg_match('@background:\s?(#[A-Za-z0-9]+)@', $style, $matches);
      if (!empty($matches[1])) {
        $this->content['background_color'] = $matches[1];
      }
      $contentHtml = $crawler->filter('.content');
      $this->content['title'] = $contentHtml->filter('h1')->eq(0)->text();
      $this->content['message'] = $contentHtml->filter('.field-name-body')->eq(0)->html();
    }
    catch (TransferException $exception) {
      throw new \Exception('Could not fetch servicespot from ' . $this->url, 1, $exception);
    }
    catch (\Exception $o_0) {
      throw new \Exception('Something went wrong trying to scrape servicespot at ' . $this->url, 1, $o_0);
    }
  }

  /**
   * Get an array of data scraped from the url.
   *
   * @return array
   *   Keyed array with values from the url.
   */
  public function getContent()
  {
    return $this->content;
  }

}
