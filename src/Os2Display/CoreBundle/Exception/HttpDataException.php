<?php
/**
 * @file
 * Contains DataException.
 */

namespace Os2Display\CoreBundle\Exception;

use JMS\Serializer\Annotation\Groups;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class DataException
 */
class HttpDataException extends HttpException {
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
   * @param int $status
   * @param \Throwable|NULL $previous
   */
  public function __construct($statusCode, $data, $message = '', \Exception $previous = NULL, array $headers = [], $code = 0) {
    parent::__construct($statusCode, $message, $previous, $headers, $code);
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
