<?php


namespace ApiMessageDispatcher\Constraint;


use Symfony\Component\Validator\Constraint;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class CustomConstraint
 * @package App\Service\Constraint
 * @Annotation
 */
class CustomConstraint extends Constraint
{

    /**
     * True to verify that an object exists
     * False to verify that an object doesn't exists
     * Null to use an other functionality
     * @var bool|null
     */
    public ?bool $exists = null;

    /**
     * True to verify that a property of an object is equals to something
     * True to verify that a property of an object is not equals to something
     * Null to use an other functionality
     * @var bool|null
     */
    public ?bool $equals = null;

    /**
     * The class on the object
     * @var string
     */
    public string $class;

    /**
     * The property filter the object to find
     * @var string
     */
    public string $property;

    /**
     * The value to compare
     * @var string|null
     */
    public ?string $value = null;

    /**
     * The property to compare
     * @var string|null
     */
    public ?string $compareProperty = null;

}