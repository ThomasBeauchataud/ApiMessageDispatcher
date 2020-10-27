<?php


namespace ApiMessageDispatcher\Service\Constraint;


use ApiMessageDispatcher\ApiMessageDispatcherException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CustomConstraintValidator extends ConstraintValidator
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * CustomConstraintValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @inheritDoc
     * @param CustomConstraint $constraint
     * @throws ApiMessageDispatcherException
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint->property == null || $constraint->class == null) {
            $exceptionContent = "property and class fields cannot be null if equals field is not null on the"
                . " annotation @CustomConstraint";
            throw new ApiMessageDispatcherException($exceptionContent);
        }
        $object = $this->em->getRepository($constraint->class)->findOneBy([$constraint->property => $value]);
        if (!is_null($constraint->exists)) {
            if (is_null($object) == $constraint->exists) {
                $this->context->buildViolation($this->buildMessage($value, $constraint))
                    ->addViolation();
            }
        }
        if (!is_null($constraint->equals)) {
            if (is_null($object)) {
                $exceptionContent = "Impossible to validate a comparison with the annotation @CustomConstraint on a "
                    . "null object";
                throw new ApiMessageDispatcherException($exceptionContent);
            }
            if ($constraint->compareProperty == null || $constraint->value == null) {
                $exceptionContent = "compareProperty and value fields cannot be null if equals field is not null on the"
                . " annotation @CustomConstraint";
                throw new ApiMessageDispatcherException($exceptionContent);
            }
            $methodName = "get" . ucwords($constraint->compareProperty);
            $comparedValue = call_user_func(array($object, $methodName));
            if (($comparedValue == $value) != $constraint->equals) {
                $this->context->buildViolation($this->buildMessage($value, $constraint))
                    ->addViolation();
            }
        }
    }

    /**
     * @param mixed $value
     * @param CustomConstraint $constraint
     * @return string
     */
    protected function buildMessage($value, CustomConstraint $constraint): string
    {
        $splitName = explode("\\", $constraint->class);
        $className = end($splitName);
        return "The " . $className . " with the " . $constraint->property . " " . $value
            . ($constraint->exists ? " doesn't" : " already") . " exists";
    }


}