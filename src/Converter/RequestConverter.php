<?php


namespace ApiMessageDispatcher\Converter;


use ApiMessageDispatcher\ApiMessageDispatcherException;
use ApiMessageDispatcher\Logger\ConverterLogger;
use ApiMessageDispatcher\Logger\LoggerInterface;
use ApiMessageDispatcher\Message\InjectParameter;
use ApiMessageDispatcher\Message\Message;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class RequestConverter
 * @package ApiMessageDispatcher\Service
 */
abstract class RequestConverter implements ParamConverterInterface
{

    private const DEFAULT_SOURCE = "converter.log";

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var AnnotationReader
     */
    protected AnnotationReader $annotationReader;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * AbstractRequestConverter constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->logger->setSource(self::DEFAULT_SOURCE);
        $this->annotationReader = new AnnotationReader();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }


    /**
     * @inheritDoc
     * @throws ApiMessageDispatcherException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $object = null;
        foreach ($this->getSupportedMessages() as $objectClass => $name) {
            if ($configuration->getName() == $name) {
                if (!class_exists($objectClass)) {
                    $exceptionContent = "Impossible to instantiate the class " . $objectClass
                        . " cause it doesn't exists";
                    throw new ApiMessageDispatcherException($exceptionContent);
                }
                $object = new $objectClass();
            }
        }
        $object = $this->enrichProperties($request, $object);
        $request->attributes->set($configuration->getName(), $object);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        foreach ($this->getSupportedMessages() as $name) {
            if ($configuration->getName() == $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set properties of the object with request parameters then return it
     *
     * @param Request $request
     * @param Message $object
     * @return mixed
     * @throws ApiMessageDispatcherException
     */
    protected function enrichProperties(Request $request, Message $object)
    {
        $parameters = json_decode($request->getContent(), true);
        try {
            $reflectionClass = new ReflectionClass($object);
        } catch (ReflectionException $e) {
            $exceptionContent = "Impossible to instantiate the class " . get_class($object) . " : " . $e->getMessage();
            throw new ApiMessageDispatcherException($exceptionContent);
        }
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            try {
                $reflectionProperty = new ReflectionProperty(get_class($object), $property->getName());
            } catch (ReflectionException $e) {
                $exceptionContent = "Impossible to instantiate the class " . get_class($object) . " : "
                    . $e->getMessage();
                throw new ApiMessageDispatcherException($exceptionContent);
            }
            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, InjectParameter::class);
            if (!is_null($annotation)) {
                if ($parameters == null) {
                    $exceptionContent = "Attempt to inject request parameters to the message " . get_class($object)
                        . " but the request content is null";
                    throw new ApiMessageDispatcherException($exceptionContent);
                }
                $methodName = "set" . ucwords($property->getName());
                try {
                    $reflectionClass->getMethod($methodName);
                } catch (ReflectionException $e) {
                    $exceptionContent = "The class using " . InjectParameter::class . " annotation on the property "
                        . $property->getName() . " must have a setter : " . $methodName;
                    throw new ApiMessageDispatcherException($exceptionContent);
                }
                if (!array_key_exists($property->getName(), $parameters)) {
                    $exceptionContent = $property . " field doesn't exists in the request content";
                    throw new ApiMessageDispatcherException($exceptionContent);
                }
                $parameter = $parameters[$property->getName()];
                call_user_func(array($object, $methodName), $parameter);
            }
        }
        $this->log($parameters, $object);
        return $object;
    }

    /**
     * @param array|null $parameters
     * @param Message $message
     */
    protected function log(?array $parameters, Message $message)
    {
        $content = "Request parameters " . $this->serializer->serialize($parameters, 'json')
            . " successfully converter to " . get_class($message) . " : "
            . $this->serializer->serialize($message, 'json');
        $this->logger->info($content);
    }

    /**
     * Return the list of ParamConverter names with their associated object to instantiate
     *
     * @return iterable
     * @example yield myMessage => "App\Message\MyMessage"
     */
    protected abstract function getSupportedMessages(): iterable;

}
