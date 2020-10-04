<?php


namespace ApiMessageDispatcher\Controller;


use ApiMessageDispatcher\Message\Message;
use ApiMessageDispatcher\Logger\WebServiceLogger;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class MessageDispatcherController
 * @package ApiMessageDispatcher\Controller
 */
abstract class MessageDispatcherController extends AbstractController
{

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var MessageBusInterface
     */
    protected MessageBusInterface $bus;

    /**
     * MessageDispatcherController constructor.
     * @param ValidatorInterface $validator
     * @param MessageBusInterface $bus
     */
    public function __construct(ValidatorInterface $validator, MessageBusInterface $bus)
    {
        $this->validator = $validator;
        $this->bus = $bus;
    }


    /**
     * @param Message $message
     * @param bool $return
     * @return Response
     */
    protected function dispatchAndReturn(Message $message, bool $return = false): Response
    {
        try {
            $errors = $this->validator->validate($message);
            if (count($errors) > 0) {
                throw new Exception($errors[0]->getMessage());
            }
            $envelope = $this->bus->dispatch($message);
            if ($return) {
                $content = $envelope->last(HandledStamp::class)->getResult();
                $response = array("response" => "OK", "content" => $content);
            } else {
                $response = array("response" => "OK");
            }
            WebServiceLogger::logResponse($response);
            return new Response(json_encode($response));
        } catch (Exception $e) {
            return new Response(json_encode(array("response" => "KO", "error" => $e->getMessage())));
        }
    }

}