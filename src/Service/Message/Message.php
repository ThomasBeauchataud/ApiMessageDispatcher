<?php


namespace ApiMessageDispatcher\Service\Message;


/**
 * Interface Message
 * @package ApiMessageDispatcher\Message
 * @author Thomas Beauchataud
 * @since 04.10.2020
 */
interface Message
{

    /**
     * Serialize a Message into an associative array
     * @return array
     */
    function serialize(): array;

}