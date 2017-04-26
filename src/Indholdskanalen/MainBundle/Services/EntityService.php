<?php
/**
 * @file
 * Contains the entity service.
 */

namespace Indholdskanalen\MainBundle\Services;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Class EntityService
 * @package Indholdskanalen\MainBundle\Services
 */
class EntityService {
  private $accessor;
  private $validator;

  /**
   * Constructor.
   */
  public function __construct(RecursiveValidator $validator) {
    $this->accessor = PropertyAccess::createPropertyAccessor();
    $this->validator = $validator;
  }

  /**
   * Set values on entity.
   *
   * @param $entity
   *   The entity to set values on.
   * @param array $values
   *   The values to set, in [name => value] array.
   * @return \Doctrine\ORM\Mapping\Entity
   *   The entity.
   */
  public function setValues($entity, $values) {
    foreach ($values as $name => $value) {
      if ($this->accessor->isWritable($entity, $name)) {
        $this->accessor->setValue($entity, $name, $value);
      }
    }

    return $entity;
  }

  /**
   * Validate an entity.
   *
   * @param $entity
   * @return mixed
   */
  public function validateEntity($entity) {
    $errors = $this->validator->validate($entity);

    return $errors;
  }
}
