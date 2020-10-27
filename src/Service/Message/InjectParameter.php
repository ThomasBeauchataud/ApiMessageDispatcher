<?php


namespace ApiMessageDispatcher\Service\Message;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Annotation to use on properties of a Message object to inject the value of the request content in it
 * threw a ParamConverter (extending RequestConverter)
 *
 * Class InjectParameter
 * @package ApiMessageDispatcher\Message
 * @Annotation
 * @Target("PROPERTY")
 * @author Thomas Beauchataud
 * @since 04.10.2020
 */
class InjectParameter
{

    public ?string $className = null;

    public ?string $propertyName = null;

    public bool $doctrine = false;

}