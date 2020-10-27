<?php


namespace ApiMessageDispatcher\Service;


use ApiMessageDispatcher\Service\Logger\WebServiceLoggerInterface;

/**
 * Class RestClient
 * @package ApiMessageDispatcher\Service
 */
class RestClient implements RestClientInterface
{

    /**
     * @var WebServiceLoggerInterface
     */
    private WebServiceLoggerInterface $logger;

    /**
     * RestClient constructor.
     * @param WebServiceLoggerInterface $logger
     */
    public function __construct(WebServiceLoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @inheritDoc
     */
    public function get(string $url, array $parameters = array()): array
    {
        $this->logger->logDetailRequest($url, "GET", $parameters);
        $ch = curl_init($url . "?" . http_build_query($parameters));
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST => FALSE
        ));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            $response = array();
        } else {
            $response = json_decode($response, true);
        }
        if (!is_array($response)) {
            $response = array();
        }
        $this->logger->logResponse($response);
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function post(string $url, array $parameters): array
    {
        $this->logger->logDetailRequest($url, "POST", $parameters);
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POSTFIELDS => json_encode($parameters),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            $response = array();
        } else {
            $response = json_decode($response, true);
        }
        if (!is_array($response)) {
            $response = array();
        }
        $this->logger->logResponse($response);
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function put(string $url, array $parameters): array
    {
        $this->logger->logDetailRequest($url, "PUT", $parameters);
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POSTFIELDS => json_encode($parameters),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        ));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            $response = array();
        } else {
            $response = json_decode($response, true);
        }
        if (!is_array($response)) {
            $response = array();
        }
        $this->logger->logResponse($response);
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $url, array $parameters): array
    {
        $this->logger->logDetailRequest($url, "DELETE", $parameters);
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POSTFIELDS => json_encode($parameters),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        ));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            $response = array();
        } else {
            $response = json_decode($response, true);
        }
        if (!is_array($response)) {
            $response = array();
        }
        $this->logger->logDetailResponse($response);
        return $response;
    }

}