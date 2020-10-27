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
     * @var EntityManagerInterface|null
     */
    protected ?EntityManagerInterface $em;

    /**
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger;

    /**
     * @var RestClientInterface|null
     */
    protected ?RestClientInterface $restClient;

    /**
     * @var ParameterBagInterface|null
     */
    protected ?ParameterBagInterface $parameters;

    /**
     * @var MessageBusInterface|null
     */
    protected ?MessageBusInterface $bus;

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
     * @param MessageBusInterface|null $bus
     */
    public function setBus(?MessageBusInterface $bus): void
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
        $content = "Handling : " . substr(strrchr(get_class($message), "\\"), 1) . " "
            . json_encode($message->serialize());
        $this->logger->info($content);
    }

}