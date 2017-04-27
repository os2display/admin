<?php
/**
 * @file
 * Contains DataException.
 */

namespace Indholdskanalen\MainBundle\Exception;

use JMS\Serializer\Annotation\Groups;

/**
 * Class DataException
 */
class DataException extends \Exception {
  /**
   * @Groups({"api"})
   */
  protected $data;

  /**
   * @Groups({"api"})
   */
  protected $message;

  /**
   * DataException constructor.
   * @param string $message
   * @param mixed $data
   * @param int $code
   * @param \Throwable|NULL $previous
   */
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
