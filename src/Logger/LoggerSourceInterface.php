<?php


namespace ApiMessageDispatcher\Logger;


interface LoggerSourceInterface
{

    /**
     * @return string
     */
    public function getSource(): string;

    /**
     * @param string $source
     */
    public function setSource(string $source): void;

}