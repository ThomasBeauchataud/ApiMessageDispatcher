<?php


namespace ApiMessageDispatcher\Constraint;


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
            throw new ApiMessageDispatcherException("");
        }
        $object = $this->em->getRepository($constraint->class)->findOneBy([$constraint->property => $value]);
        if ((is_null($object) && $constraint->exists) || (!is_null($object) && !$constraint->exists)) {
            $this->context->buildViolation($this->buildMessage($value, $constraint))
                ->addViolation();
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