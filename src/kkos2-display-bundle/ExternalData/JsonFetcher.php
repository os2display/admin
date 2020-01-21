<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class JsonFetcher
{

  protected static function addQueryData($url, $queryData) {
    $url_parts = parse_url($url);
    $query = empty($url_parts['query']) ? '' : $url_parts['query'];
    parse_str($query, $params);
    $params += $queryData;

    return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . http_build_query($params);
  }

  public static function fetch($url, $queryData = [])
  {
    try {
      $client = new Client();
      $url = self::addQueryData($url, $queryData);
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
