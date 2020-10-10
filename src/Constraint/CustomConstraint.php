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

    public bool $exists;

    public string $class;

    public string $property;

}