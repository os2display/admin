<?php
/**
 * @file
 * Contains DataException.
 */

namespace Indholdskanalen\MainBundle\Exception;

/**
 * Class DataException
 */
class DataException extends \Exception {
  protected $data;

  public function __construct($message = "", $data, $code = 0, \Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
    $this->data = $data;
  }

  /**
   * @return mixed
   */
  public function getData() {
    return $this->data;
  }

  /**
   * @param mixed $data
   */
  public function setData($data) {
    $this->data = $data;
  }
}
