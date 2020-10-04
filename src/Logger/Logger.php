<?php


namespace ApiMessageDispatcher\Logger;


use DateTime;
use Exception;

class Logger implements LoggerInterface
{

    private const DEFAULT_PATH = "../var/log/";

    /**
     * @var string|null
     */
    protected ?string $source;

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->source = null;
    }


    /**
     * @inheritDoc
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @inheritDoc
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = array())
    {
        // TODO: Implement emergency() method.
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = array())
    {
        // TODO: Implement alert() method.
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = array())
    {
        // TODO: Implement critical() method.
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = array())
    {
        // TODO: Implement error() method.
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = array())
    {
        // TODO: Implement warning() method.
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = array())
    {
        // TODO: Implement notice() method.
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function info($message, array $context = array())
    {
        $this->log("INFO", $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = array())
    {
        // TODO: Implement debug() method.
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->source == null) {
            throw new Exception("You must define the source of the logger before logging with it");
        }
        $file = fopen(self::DEFAULT_PATH . $this->source . ".log", 'a');
        $date = new DateTime();
        fwrite($file, "[".$date->format('Y-m-d H:i:s') . "] " . $level . ": " . $message . "\n");
        fclose($file);
    }

}