<?php


namespace App\Logger;


interface LoggerInterface extends \Psr\Log\LoggerInterface
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