<?php
namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

/**
 * Class DataFetcher
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\ExternalData
 */
class DataFetcher
{

  /**
   * Convenience method to get response body from url.
   *
   * @param string $url
   *
   * @return string
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getBody($url) {
    $client = new Client();
    $res = $client->request('GET', $url);

    if ($res->getStatusCode() !== 200) {
      $reason = $res->getReasonPhrase() ?: '';
      throw new TransferException("The url $url responded with a non-200 status code. Code was: {$res->getStatusCode()} $reason");
    }

    return $res->getBody()->getContents();
  }

}
