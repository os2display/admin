<?php

namespace Reload\Os2DisplaySlideTools\Slides;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class DataFetcher
{

  /**
   * Fetch and load the XML from the feed.
   *
   * @throws \Exception
   *   If anything went wrong fetching the XML.
   */
  public static function fetchSimpleXml($url)
  {
    $xml = false;
    try {
      $guzzler = new Client();
      $response = $guzzler->get($url, [
        'headers' => [
          'Accept' => 'application/xml'
        ]
      ]);
      $body = $response->getBody();
      $contents = (string) $body;
      libxml_use_internal_errors(true);
      $xml = simplexml_load_string($contents);
    } catch (TransferException $exception) {
      throw new \Exception('Could not fetch XML from: ' . $url . ' E: ' . $exception->getMessage());
    }

    // If the parsing failed, then try to log the errors and then throw an
    // exception.
    if (false === $xml) {
      $errors = [];
      foreach (libxml_get_errors() as $error) {
        $errors[] = $error->message;
      }
      throw new \UnexpectedValueException('An error occured when trying to parse XML: ' . $url);
    }
    return $xml;
  }

}