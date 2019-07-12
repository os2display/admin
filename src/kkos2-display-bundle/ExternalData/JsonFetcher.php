<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class JsonFetcher
{

  protected static function addCacheBuster($url){
    $url_parts = parse_url($url);
    $params = [];
    parse_str($url_parts['query'], $params);

    $params['cache_buster'] = time();
    $url_parts['query'] = http_build_query($params);
    return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
  }

  public static function fetch($url)
  {
    try {
      $client = new Client();
      $url = static::addCacheBuster($url);
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
