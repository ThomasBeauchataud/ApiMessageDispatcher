<?php


namespace ApiMessageDispatcher\Service\Handler;


use ApiMessageDispatcher\Service\Logger\LoggerInterface;
use ApiMessageDispatcher\Service\Message\Message;
use ApiMessageDispatcher\Service\RestClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class MessageHandler implements MessageSubscriberInterface
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
        $this->init();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        $this->init();
    }

    /**
     * @param RestClientInterface $restClient
     */
    public function setRestClient(RestClientInterface $restClient): void
    {
        $this->restClient = $restClient;
        $this->init();
    }

    /**
     * @param ParameterBagInterface $parameters
     */
    public function setParameters(ParameterBagInterface $parameters): void
    {
        $this->parameters = $parameters;
        $this->init();
    }

    /**
     * @param MessageBusInterface $bus
     */
    public function setBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }

    /**
     * Run the initialize if the container is set
     */
    protected function init(): void
    {
        if (
            !is_null($this->logger)
            && !is_null($this->parameters)
            && !is_null($this->restClient)
            && !is_null($this->bus)
            && !is_null($this->em)
        ) {
            $this->initialize();
        }
    }

    /**
     * Initialize the handler post construction
     */
    protected abstract function initialize(): void;

    /**
     * @param Message $message
     */
    protected function log(Message $message): void
    {
        $content = "Handling : " . substr(strrchr(get_class($message), "\\"), 1) . " "
            . json_encode($message->serialize());
        $this->logger->info($content);
    }

}