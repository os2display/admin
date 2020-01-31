<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class JsonFetcher
{

  public static function fetch($url, $queryData = [])
  {
      $client = new Client();
    $url_parts = parse_url($url);
    $query = empty($url_parts['query']) ? '' : $url_parts['query'];
    parse_str($query, $params);
    $params += $queryData;

      $response = $client->request('GET', $url, [
        'query' => $params,
        'headers' => [
          'Accept' => 'application/json'
        ]
      ]);

      $body = $response->getBody();
      return json_decode($body, true);
  }
}
