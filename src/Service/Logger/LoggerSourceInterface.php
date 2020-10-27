<?php


namespace ApiMessageDispatcher\Service\Logger;


interface LoggerSourceInterface
{

    /**
     * @return string
     */
    function getSource(): string;

    /**
     * @param string $source
     */
    function setSource(string $source): void;

}