<?php

/**
 * @author Guven Atbakan <guven@atbakan.com>
 */
interface SmsSenderInterface
{
    /**
     * SmsSenderInterface constructor.
     * @param string $message
     * @param string $gsmnumber
     */
    public function __construct($message, $gsmnumber);

    /**
     * @return mixed
     */
    public function send();

    /**
     * @return mixed
     */
    public function balance();

    /**
     * @param $msgId
     * @return mixed
     */
    public function report($msgId);

    /**
     * @param $number
     * @return mixed
     */
    public function utilgsmnumber($number);

    /**
     * @param $message
     * @return mixed
     */
    public function utilmessage($message);
}