<?php


namespace ApiMessageDispatcher\Logger;


class WebServiceLogger
{

    private const PATH = "../var/log/web_service.log";

    /**
     * @param string $url
     * @param array|null $parameters
     * @param string|null $method
     */
    public static function logRequest(string $url, ?array $parameters = array(), string $method = "get"): void
    {
        $content = "Receiving " . $method . " request " . $url . " with parameters "
            . json_encode($parameters);
        self::log($content);
    }

    /**
     * @param array|null $response
     */
    public static function logResponse(?array $response): void
    {
        if ($response == null) {
            $content = "Responding null response";
        } else {
            $content = "Responding " . json_encode($response);
        }
        self::log($content);
    }

    /**
     * @param string $content
     */
    private static function log(string $content): void
    {
        $file = fopen(self::PATH, 'a');
        fwrite($file, $content . "\n");
        fclose($file);
    }

}