<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class JsonFetcher
{

  protected static function addCacheBuster($url){
    $url_parts = parse_url($url);
    $query = empty($url_parts['query']) ? '' : $url_parts['query'];
    parse_str($query, $params);

    $params['cache_buster'] = time();
    return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . http_build_query($params);
  }

  public static function fetch($url)
  {
    try {
      $client = new Client();

      $url = self::addCacheBuster($url);
      $response = $client->get($url, [
        'headers' => [
          'Accept' => 'application/json'
        ]
      ]);

      $body = $response->getBody();
      return json_decode($body, true);

    } catch (TransferException $exception) {
      throw new \Exception('Error on fetch from: ' . $url);
    }
  }
}
