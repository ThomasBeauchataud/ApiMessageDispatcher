<?php


namespace ApiMessageDispatcher\Logger;


use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request $request
     * @throws Exception
     */
    public function logRequest(Request $request): void
    {
        $content = "Receiving " . $request->getMethod(). " request " . $request->getRequestUri() . " with parameters "
            . $request->getContent();
        $this->info($content);
    }

    /**
     * @param Response $response
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

}