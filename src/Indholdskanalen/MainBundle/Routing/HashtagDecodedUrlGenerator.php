<?php
/**
 * @file
 * Contains HashtagDecodedUrlGenerator which overrides UrlGenerator to include # decoding.
 */

namespace Indholdskanalen\MainBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGenerator;

class HashtagDecodedUrlGenerator extends UrlGenerator {
  /**
   * {@inheritdoc}
   */
  protected $decodedChars = array(
    // the slash can be used to designate a hierarchical structure and we want allow using it with this meaning
    // some webservers don't allow the slash in encoded form in the path for security reasons anyway
    // see http://stackoverflow.com/questions/4069002/http-400-if-2f-part-of-get-url-in-jboss
    '%2F' => '/',
    // the following chars are general delimiters in the URI specification but have only special meaning in the authority component
    // so they can safely be used in the path in unencoded form
    '%40' => '@',
    '%3A' => ':',
    // these chars are only sub-delimiters that have no predefined meaning and can therefore be used literally
    // so URI producing applications can use these chars to delimit subcomponents in a path segment without being encoded for better readability
    '%3B' => ';',
    '%2C' => ',',
    '%3D' => '=',
    '%2B' => '+',
    '%21' => '!',
    '%2A' => '*',
    '%7C' => '|',
    // Added hashtag decoding.
    '%23' => '#',
  );
}
