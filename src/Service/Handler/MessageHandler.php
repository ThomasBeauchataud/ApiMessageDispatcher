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
     * MessageHandler constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param RestClientInterface $restClient
     * @param ParameterBagInterface $parameters
     * @param MessageBusInterface $bus
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, RestClientInterface $restClient, ParameterBagInterface $parameters, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->restClient = $restClient;
        $this->parameters = $parameters;
        $this->bus = $bus;
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