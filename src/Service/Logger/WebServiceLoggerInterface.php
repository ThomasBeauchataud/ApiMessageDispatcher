<?php


namespace ApiMessageDispatcher\Service\Logger;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

interface WebServiceLoggerInterface extends LoggerSourceInterface
{

    /**
     * @param Request $request
     * @throws Exception
     */
    function logIncomingRequest(Request $request): void;

    /**
     * @param string $url
     * @param string $method
     * @param array $parameters
     */
    function logOutgoingRequest(string $url, string $method, array $parameters = array()): void;

    /**
     * @param Response $response
     * @throws Exception
     */
    function logOutgoingResponse(Response $response): void;

    /**
     * @param array|null $response
     */
    function logIncomingResponse(?array $response): void;

}