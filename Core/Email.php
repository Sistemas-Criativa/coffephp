<?php

namespace Core;

use Config\Config;

class Email
{
    private static $to = [];
    private static $subject = "";
    private static $message = "";
    private static $header = "";

    /**
     * Define a destination to email
     */
    public final static function to(string $to)
    {
        self::$to[] = $to;
        return new static;
    }

    /**
     * Define a subject to email
     */
    public final static function subject(string $subject)
    {
        self::$subject = $subject;
        return new static;
    }
    /**
     * Define a message to email
     */
    public final static function message(string $message)
    {
        self::$message = $message;
        return new static;
    }
    /**
     * Send an email
     */
    public final static function send(bool $isHTML = true): void
    {
        ob_get_clean();
        if (empty(self::$header)) {
            self::$header = "From: " . Config::config('from_email') . "\r\n";
        }
        if ($isHTML) {
            self::$header .= "MIME-Version: 1.0\r\n";
            self::$header .= "Content-type: text/html; charset=UTF-8\r\n";
        }
        foreach (self::$to as $destinatary) {
            try {
                mail($destinatary, self::$subject, self::$message, self::$header);
            } catch (\Exception $e) {

                throw new \Exception("The e-mail server returned an error", 5);
            }
        }
    }
}
