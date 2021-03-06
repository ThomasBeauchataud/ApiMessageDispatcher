<?php


namespace ApiMessageDispatcher\Service\Handler;


use ApiMessageDispatcher\Service\Logger\LoggerInterface;
use ApiMessageDispatcher\Service\Message\Message;
use ApiMessageDispatcher\Service\RestClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class MessageHandler
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var RestClientInterface
     */
    protected RestClientInterface $restClient;

    /**
     * @var ParameterBagInterface
     */
    protected ParameterBagInterface $parameters;

    /**
     * @var MessageBusInterface
     */
    protected MessageBusInterface $bus;

    /**
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param RestClientInterface $restClient
     */
    public function setRestClient(RestClientInterface $restClient): void
    {
        $this->restClient = $restClient;
    }

    /**
     * @param ParameterBagInterface $parameters
     */
    public function setParameters(ParameterBagInterface $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @param MessageBusInterface $bus
     */
    public function setBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }

    /**
     * Initialize the handler post construction
     */
    public abstract function initialize(): void;

    /**
     * @param Message $message
     */
    protected function log(Message $message): void
    {
        $this->logArray($message->serialize(), substr(strrchr(get_class($message), "\\"), 1));
    }

    /**
     * @param array $content
     * @param string $objectClassName
     */
    protected function logArray(array $content, string $objectClassName): void
    {
        $content = "Handling : " . $objectClassName . " " . json_encode($content);
        $this->logger->info($content);
    }

}