<?php


namespace ApiMessageDispatcher\Service\Logger;



use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class WebServiceLogger extends AbstractLogger implements WebServiceLoggerInterface
{

    protected string $path = "web_service";

    /**
     * WebServiceLogger constructor.
     */
    public function __construct()
    {
        $this->setSource($this->path);
    }


    /**
     * @inheritDoc
     * @throws Exception
     */
    public function logRequest(Request $request): void
    {
        $content = "Receiving " . $request->getMethod(). " request " . $request->getRequestUri() . " with parameters "
            . $request->getContent();
        $this->info($content);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function logDetailRequest(string $url, string $method, array $parameters = array()): void
    {
        $content = "Requesting " . $method . " " . $url . " with parameters " . json_encode($parameters);
        $this->info($content);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function logResponse(Response $response): void
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
    public function logDetailResponse(?array $response): void
    {
        if ($response == null) {
            $content = "Received null response";
        } else {
            $content = "Received response " . json_encode($response);
        }
        $this->info($content);
    }

}