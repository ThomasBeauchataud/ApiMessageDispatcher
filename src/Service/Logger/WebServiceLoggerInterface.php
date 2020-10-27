<?php


namespace ApiMessageDispatcher\Service\Logger;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface WebServiceLoggerInterface extends LoggerSourceInterface
{

    /**
     * @param Request $request
     * @throws Exception
     */
    function logRequest(Request $request): void;

    /**
     * @param string $url
     * @param string $method
     * @param array $parameters
     */
    function logDetailRequest(string $url, string $method, array $parameters = array()): void;

    /**
     * @param Response $response
     * @throws Exception
     */
    function logResponse(Response $response): void;

    /**
     * @param array|null $response
     */
    function logDetailResponse(?array $response): void;

}