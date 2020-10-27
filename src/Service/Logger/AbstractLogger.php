<?php


namespace ApiMessageDispatcher\Service\Logger;


use DateTime;
use Exception;

abstract class AbstractLogger implements LoggerSourceInterface, \Psr\Log\LoggerInterface
{

    /**
     * The root logger path
     */
    protected const ROOT_PATH = "../var/log/";

    /**
     * @var string
     */
    protected string $source;

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
        $this->createNewSourcePath();
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
     * @throws Exception
     */
    public function error($message, array $context = array())
    {
        $this->log("ERROR", $message, $context);
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
        $file = fopen($this->getFinalPath(), 'a');
        $date = new DateTime();
        fwrite($file, "[".$date->format('Y-m-d H:i:s') . "] " . $level . ": " . $message . "\n");
        fclose($file);
    }

    /**
     * Return the full path to the log file
     * @return string
     */
    protected function getFinalPath(): string
    {
        return self::ROOT_PATH . $this->source . ".log";
    }

    /**
     * Create directories to the new path
     */
    protected function createNewSourcePath(): void
    {
        $currentPath = self::ROOT_PATH;
        $folders = explode("/", $this->source);
        foreach($folders as $folder) {
            if (array_search($folder, $folders) == count($folders) - 1) {
                continue;
            }
            if (!in_array($folder, scandir($currentPath))) {
                $currentPath .= "/" . $folder;
                mkdir($currentPath);
            } else {
                $currentPath .= "/" . $folder;
            }
        }
    }

}