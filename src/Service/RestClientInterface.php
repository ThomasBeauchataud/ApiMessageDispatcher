<?php


namespace ApiMessageDispatcher\Service;


/**
 * Interface RestClientInterface
 * @package ApiMessageDispatcher\Service
 */
interface RestClientInterface
{

    /**
     * Execute a GET api request on a url and return the content as an associative array
     * @param string $url
     * @param array $parameters
     * @return array
     */
    function get(string $url, array $parameters = array()): array;

    /**
     * Execute a POST api request on a url and return the content as an associative array
     * @param string $url
     * @param array $parameters
     * @return array
     */
    function post(string $url, array $parameters): array;

    /**
     * Execute a PUT api request on a url and return the content as an associative array
     * @param string $url
     * @param array $parameters
     * @return array
     */
    function put(string $url, array $parameters): array;

    /**
     * Execute a DELETE api request on a url and return the content as an associative array
     * @param string $url
     * @param array $parameters
     * @return array
     */
    function delete(string $url, array $parameters): array;

}