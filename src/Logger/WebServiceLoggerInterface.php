<?php


namespace ApiMessageDispatcher\Logger;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface WebServiceLoggerInterface extends LoggerSourceInterface
{

    /**
     * @param Request $request
     * @throws Exception
     */
    public function logRequest(Request $request): void;

    /**
     * @param Response $response
     * @throws Exception
     */
    public function logResponse(Response $response): void;

}