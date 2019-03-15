<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class JsonFetcher
{

  public static function fetch($url)
  {
    try {
      $client = new Client();
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
