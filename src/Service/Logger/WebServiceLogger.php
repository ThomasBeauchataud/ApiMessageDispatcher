<?php


namespace ApiMessageDispatcher\Service\Logger;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class WebServiceLogger extends AbstractLogger implements WebServiceLoggerInterface
{

    /**
     * @var string
     */
    protected string $path = "web_service";

    /**
     * WebServiceLogger constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct($parameterBag);
        $this->setSource($this->path);
    }


    /**
     * @inheritDoc
     * @throws Exception
     */
    public function logIncomingRequest(Request $request): void
    {
        $content = "Receiving " . $request->getMethod() . " request " . $request->getRequestUri() . " with parameters "
            . ($request->getContent() == '' ? json_encode($request->request->all()) : $request->getContent());
        $this->info($content);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function logOutgoingRequest(string $url, string $method, array $parameters = array()): void
    {
        $content = "Requesting " . $method . " " . $url . " with parameters " . json_encode($parameters);
        $this->info($content);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function logOutgoingResponse(Response $response): void
    {
        if ($response == null) {
            $content = "Responding null response";
        } else {
            $content = "Responding " . json_encode($response->getContent());
        }
        $this->info($content);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function logIncomingResponse(?array $response): void
    {
        if ($response == null) {
            $content = "Received null response";
        } else {
            $content = "Received response " . json_encode($response);
        }
        $this->info($content);
    }

}