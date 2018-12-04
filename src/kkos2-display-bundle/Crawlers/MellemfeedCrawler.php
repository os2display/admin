<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Crawlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MellemfeedCrawler
 *
 * Crawls the HTML in "Mellemfeeds". A mellemfeed is a temporary placeholder we
 * use for links to service spots on KK. It is a "Servicesituation" content
 * type in the KK multisites and should be formatted like this:
 *
 * Put a H2 tag with a title and some links in a blockquote. Title can be
 * anything you need a link collection for.
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\Crawlers
 */
class MellemfeedCrawler
{
  /**
   * @var string $feedUrl
   */
  private $feedUrl;

  /**
   * @var \GuzzleHttp\Client $guzzler
   */
  private $guzzler;

  /**
   * @var string $html
   */
  private $html;

  /**
   * @var array $linkGroups
   */
  private $linkGroups = [];


  /**
   * Construct.
   *
   * @param string $feedUrl
   */
  public function __construct($feedUrl)
  {
    $this->feedUrl = $feedUrl;
    $this->guzzler = new Client();
  }

  /**
   * Fetch and crawl the mellemfeed.
   *
   * @throws \Exception
   *   If the feed could not be fetched or scraped.
   */
  public function crawl()
  {
    try {
      $response = $this->guzzler->get($this->feedUrl);
      $body = $response->getBody();
      $this->html = (string)$body;
      $crawler = new Crawler($this->html);
      $blockquotes = $crawler->filter(('#main-content .node-service-spot blockquote'));

      $blockquotes->reduce(function (Crawler $node, $i) {
        $key = strtolower($node->filter('h2')->text());
        $anchorTags = $node->filter('a');
        $links = $anchorTags->each(function (Crawler $a, $i) {
          return $a->text();
        });
        $this->linkGroups[$key] = $links;
      });
    } catch (TransferException $exception) {
      throw new \Exception('Could not fetch mellemfeed from ' . $this->feedUrl, 1, $exception);
    }
    catch (\Exception $o_0) {
      throw new \Exception('Something went wrong trying to scrape ' . $this->feedUrl, 1, $o_0);
    }
  }

  /**
   * Get the links crawled in named groups.
   *
   * @return array
   *   An array of links grouped by the h2 value in the group in the mellemfeed.
   */
  public function getLinkGroups()
  {
    return $this->linkGroups;
  }

}
