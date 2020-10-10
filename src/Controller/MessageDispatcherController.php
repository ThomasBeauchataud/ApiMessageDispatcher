<?php


namespace ApiMessageDispatcher\Controller;


use ApiMessageDispatcher\Logger\WebServiceLoggerInterface;
use ApiMessageDispatcher\Message\Message;
use ApiMessageDispatcher\Logger\ConverterLogger;
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
     * MessageDispatcherController constructor.
     * @param ValidatorInterface $validator
     * @param WebServiceLoggerInterface $logger
     */
    public function __construct(ValidatorInterface $validator, WebServiceLoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger = $logger;
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

}