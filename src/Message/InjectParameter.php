<?php


namespace ApiMessageDispatcher\Message;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Annotation to use an a Message who will be converter from a Request threw a ParamConverter to autoload a field of
 * the message with the JSON content of the Request
 *
 * Class AutoLoad
 * @package ApiMessageDispatcher\Message
 * @Annotation
 * @Target("PROPERTY")
 */
class InjectParameter
{

}