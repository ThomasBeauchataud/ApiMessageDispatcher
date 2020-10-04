<?php


namespace ApiMessageDispatcher\Controller;


use ApiMessageDispatcher\Message\Message;
use ApiMessageDispatcher\Logger\ConverterLogger;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     * MessageDispatcherController constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
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
            $envelope = $this->dispatchMessage($message);
            if ($return) {
                $content = $envelope->last(HandledStamp::class)->getResult();
                $response = array("response" => "OK", "content" => $content);
            } else {
                $response = array("response" => "OK");
            }
            return new Response(json_encode($response));
        } catch (Exception $e) {
            return new Response(json_encode(array("response" => "KO", "error" => $e->getMessage())));
        }
    }

}