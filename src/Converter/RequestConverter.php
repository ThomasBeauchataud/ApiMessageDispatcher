<?php


namespace ApiMessageDispatcher\Converter;


use ApiMessageDispatcher\Logger\WebServiceLogger;
use ApiMessageDispatcher\Message\Message;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestConverter
 * @package ApiMessageDispatcher\Service
 */
abstract class RequestConverter implements ParamConverterInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * AbstractRequestConverter constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $className = $this->getMatchNamespace() . "\\" . $this->getMatchClass();
        $object = new $className();
        $object = $this->enrichProperties($request, $object);
        $request->attributes->set($configuration->getName(), $object);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getName() == $this->getMatchName();
    }

    /**
     * Set properties of the object with request parameters then return it
     * @param Request $request
     * @param Message $object
     * @return mixed
     */
    protected function enrichProperties(Request $request, Message $object)
    {
        $parameters = json_decode($request->getContent(), true);
        if ($parameters == null) {
            return $object;
        }
        WebServiceLogger::logRequest($request->getRequestUri(), $parameters, $request->getMethod());
        foreach($object->getProperties() as $property) {
            $method = "set" . ucwords($property);
            $parameter = array_key_exists($property, $parameters) ? $parameters[$property] : null;
            call_user_func(array($object, $method), $parameter);
        }
        return $object;
    }

    /**
     * Return the name of the converter (the name of the class to convert)
     * @return string
     */
    protected function getMatchName(): string
    {
        return lcfirst($this->getMatchClass());
    }

    /**
     * Return the name of the class to instance without the namespace
     * @return string
     */
    protected function getMatchClass(): string
    {
        $class = substr(strrchr(get_class($this), "\\"), 1);
        $matches = array();
        preg_match_all('/[A-Z]/', $class, $matches, PREG_OFFSET_CAPTURE);
        return substr($class, 0, $matches[0][count($matches[0]) - 1][1]);
    }

    /**
     * Return the name of the namespace of the class to instance
     * @return string
     */
    protected function getMatchNamespace(): string
    {
        $class = $this->getMatchClass();
        $matches = array();
        preg_match_all('/[A-Z]/', $class, $matches, PREG_OFFSET_CAPTURE);
        $bundleName = substr($class, 0, $matches[0][1][1]);
        return "ApiMessageDispatcher\Service\\" . $bundleName . "\Message" ;
    }

}
