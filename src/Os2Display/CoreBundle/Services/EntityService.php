<?php
/**
 * @file
 * Contains the entity service.
 */

namespace Os2Display\CoreBundle\Services;

use Os2Display\CoreBundle\Exception\ValidationException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Class EntityService
 *
 * @package Os2Display\CoreBundle\Services
 */
class EntityService {
  private $accessor;
  private $validator;

  /**
   * EntityService constructor.
   *
   * @param \Symfony\Component\Validator\Validator\RecursiveValidator $validator
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
   * @param array|NULL $properties
   *   The names to set.
   * @return \Doctrine\ORM\Mapping\Entity
   *   The entity.
   */
  public function setValues($entity, $values, array $properties = NULL) {
    foreach ($values as $name => $value) {
      if ($properties === NULL || in_array($name, $properties)) {
        if ($this->accessor->isWritable($entity, $name)) {
          $this->accessor->setValue($entity, $name, $value);
        }
      }
    }

    return $entity;
  }

  /**
   * Validate an entity.
   *
   * @param $entity
   * @return \Symfony\Component\Validator\ConstraintViolationListInterface
   * @throws \Os2Display\CoreBundle\Exception\ValidationException
   */
  public function validateEntity($entity) {
    $errors = $this->validator->validate($entity);

    if (count($errors) > 0) {
      throw new ValidationException('Validation exceptions', $errors);
    }

    return $errors;
  }
}
