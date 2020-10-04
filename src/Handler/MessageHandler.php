<?php


namespace ApiMessageDispatcher\Handler;


use ApiMessageDispatcher\Logger\LoggerInterface;
use ApiMessageDispatcher\Message\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

abstract class MessageHandler implements MessageSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * OrderManagementHandler constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param Message $message
     */
    protected function log(Message $message): void
    {
        $content = "Handling : " . substr(strrchr(get_class($message), "\\"), 1) . " "
            . $this->serializer->serialize($message, 'json');
        $this->logger->info($content);
    }

}