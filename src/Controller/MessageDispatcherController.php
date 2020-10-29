<?php


namespace ApiMessageDispatcher\Controller;


use ApiMessageDispatcher\Service\Logger\WebServiceLoggerInterface;
use ApiMessageDispatcher\Service\Message\Message;
use ApiMessageDispatcher\Service\Logger\ConverterLogger;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class MessageDispatcherController
 * @package ApiMessageDispatcher\Controller
 * @author Thomas Beauchataud
 * @since 04.10.2020
 */
abstract class MessageDispatcherController extends AbstractController
{

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var WebServiceLoggerInterface
     */
    protected WebServiceLoggerInterface $logger;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * @param WebServiceLoggerInterface $logger
     */
    public function setLogger(WebServiceLoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @param Message $message
     * @param bool $return
     * @return Response
     * @throws Exception
     */
    protected function dispatchAndReturn(Request $request, Message $message, bool $return = false): Response
    {
        $this->logger->logRequest($request);
        try {
            $errors = $this->validator->validate($message);
            if (count($errors) > 0) {
                throw new Exception($errors[0]->getMessage());
            }
            $envelope = $this->dispatchMessage($message);
            if ($return) {
                $content = $envelope->last(HandledStamp::class)->getResult();
                $response = array("response" => "OK", "content" => $content);
            } else {
                $response = array("response" => "OK");
            }
            $response = new Response(json_encode($response));
        } catch (Exception $e) {
            $response = new Response(json_encode(array("response" => "KO", "error" => $e->getMessage())));
        }
        $this->logger->logResponse($response);
        return $response;
    }

    /**
     * @param Request $request
     * @param Message $message
     * @return mixed
     * @throws Exception
     */
    protected function dispatch(Request $request, Message $message)
    {
        $this->logger->logRequest($request);
        $errors = $this->validator->validate($message);
        if (count($errors) > 0) {
            throw new Exception($errors[0]->getMessage());
        }
        $envelope = $this->dispatchMessage($message);
        return $envelope->last(HandledStamp::class)->getResult();
    }

}