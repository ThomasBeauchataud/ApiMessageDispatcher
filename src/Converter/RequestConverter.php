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
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Abstract class converting request and his parameters to message with injected properties as setup
 *
 * Class RequestConverter
 * @package ApiMessageDispatcher\Service
 * @author Thomas Beauchataud
 * @since 04.10.2020
 */
abstract class RequestConverter implements ParamConverterInterface
{

    private const DEFAULT_SOURCE = "converter";

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
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $object = null;
        foreach ($this->getSupportedMessages() as $name => $objectClass) {
            if ($configuration->getName() == $name) {
                $object = $this->instantiateClass($objectClass);
            }
        }
        if (!($object instanceof Message)) {
            $exceptionContent = "Provided class in " . RequestConverter::class
                . ":getSupportedMessages must be instance of " . Message::class;
            throw new ApiMessageDispatcherException($exceptionContent);
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
        foreach ($this->getSupportedMessages() as $name => $object) {
            if ($configuration->getName() == $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set properties of the object with request parameters then return it
     *
     * @param Request $request The request with parameters
     * @param Message $object The object to enrich
     * @return object The object with all his properties injected
     * @throws ApiMessageDispatcherException
     */
    protected function enrichProperties(Request $request, Message $object): object
    {
        $parameters = array_merge(
            json_decode($request->getContent(), true) == null ? array() : json_decode($request->getContent(), true) ,
            $request->query->all(),
            $request->request->all()
        );
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
            /** @var InjectParameter $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, InjectParameter::class);
            if (!is_null($annotation)) {
                $this->validateParameters($parameters, $property);
                $this->validateAnnotation($annotation);
                $parameter = $parameters[$property->getName()];
                $object = $this->injectProperty($property->getName(), $parameter, $object, $annotation);
            }
        }
        $this->log($parameters, $object);
        return $object;
    }

    /**
     * Validate the annotation content before trying to use it as injection rule
     *
     * @param InjectParameter $injectParameter The annotation to validate
     * @throws ApiMessageDispatcherException If the annotation isn't valid
     */
    protected function validateAnnotation(InjectParameter $injectParameter): void
    {
        if ($injectParameter->propertyName != null && $injectParameter->className == null) {
            $exceptionContent = "Impossible to set the field propertyName on the annotation "
                . get_class($injectParameter) . " when the the field className is null";
            throw new ApiMessageDispatcherException($exceptionContent);
        }
        if ($injectParameter->doctrine && ($injectParameter->className == null || $injectParameter->propertyName == null)) {
            $exceptionContent = "Impossible to set the field doctrine to true on the annotation "
                . get_class($injectParameter) . " when the the field className or propertyName are null";
            throw new ApiMessageDispatcherException($exceptionContent);
        }
    }

    /**
     * Validate the request parameters before trying to inject the property value
     *
     * @param array|null $parameters The request parameters
     * @param ReflectionProperty $property The property to inject
     */
    protected function validateParameters(?array &$parameters, ReflectionProperty $property): void
    {
        if ($parameters == null) {
            $parameters = array();
        }
        if (!array_key_exists($property->getName(), $parameters)) {
            $parameters[$property->getName()] = null;
        }
    }

    /**
     * Inject a property in an object
     * If the annotation is not null, using the annotation injection rules
     *
     * @param string $propertyName The name of the property to inject
     * @param mixed $propertyValue The value of the property to inject
     * @param object $object The object owning the property to inject
     * @param InjectParameter|null $annotation The InjectParameter annotation which override injection rules
     * @return object Returning the object with injected property
     * @throws ApiMessageDispatcherException If the object doesn't have a setter method for the property
     */
    protected function injectProperty(string $propertyName, $propertyValue, object $object, InjectParameter $annotation = null): object
    {
        if ($annotation == null || $annotation->propertyName == null) {
            $methodName = "set" . ucwords($propertyName);
            try {
                $reflectionClass = new ReflectionClass($object);
            } catch (ReflectionException $e) {
                $exceptionContent = "Impossible to instantiate the class " . get_class($object) . " : " . $e->getMessage();
                throw new ApiMessageDispatcherException($exceptionContent);
            }
            try {
                $reflectionClass->getMethod($methodName);
            } catch (ReflectionException $e) {
                $exceptionContent = "The class " . get_class($object) . " doesn't have any setter for the property "
                    . $propertyName . ", expecting a method named " . $methodName;
                throw new ApiMessageDispatcherException($exceptionContent);
            }
            call_user_func(array($object, $methodName), $propertyValue);
        } else {
            $subObject = $this->instantiateClass($annotation->className);
            if ($annotation->doctrine) {
                $subObjects = $this->em->getRepository($annotation->className)
                    ->findBy([$annotation->propertyName => $propertyValue]);
                if (count($subObjects) == 1) {
                    $object = $this->injectProperty($propertyName, $subObjects[0], $object);
                } else {
                    $object = $this->injectProperty($propertyName, $subObjects, $object);
                }
            } else {
                $subObject = $this->injectProperty($annotation->propertyName, $subObject, $propertyValue);
                $object = $this->injectProperty($propertyName, $subObject, $object);
            }
        }
        return $object;
    }

    /**
     * Instantiate a class with his name
     *
     * @param string $className The name of the class to instantiate
     * @return object Return the instantiate class
     * @throws ApiMessageDispatcherException If the class doesn't exists
     */
    protected function instantiateClass(string $className): object
    {
        if (!class_exists($className)) {
            $exceptionContent = "Impossible to instantiate the class " . $className
                . " cause it doesn't exists";
            throw new ApiMessageDispatcherException($exceptionContent);
        }
        return new $className();
    }

    /**
     * Log every conversion from request parameters to the instantiated object
     *
     * @param array|null $parameters The request parameters
     * @param Message $message The message generated
     */
    protected function log(?array $parameters, Message $message): void
    {
        try {
            $serializedObject = $this->serializer->serialize($message, 'json');
        } catch (Exception $ignored) {
            $serializedObject = $this->serializer->serialize($message, 'json', array('groups' => get_class($message)));
        }
        $content = "Request parameters " . $this->serializer->serialize($parameters, 'json')
            . " successfully converter to " . get_class($message) . " : "
            . $serializedObject;
        $this->logger->info($content);
    }

    /**
     * Return the list of ParamConverter names with their associated object to instantiate
     *
     * @return iterable The list of covered converter
     * @example yield "myMessage" => "MyMessage::class"
     */
    protected abstract function getSupportedMessages(): iterable;

}
