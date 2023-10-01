<?php

namespace App\Mail;

class MailObject
{
    public $to;
    public $subject;
    public $body;

    public function __construct($to, $subject, $body)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
    }
}
