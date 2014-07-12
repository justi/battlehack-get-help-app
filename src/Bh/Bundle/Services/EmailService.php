<?php

namespace Bh\Bundle\Services;

use SendGrid;
use SendGrid\Email;

class EmailService
{
    private $sg;
    public function __construct($user, $pass)
    {
        $this->sg = new SendGrid($user, $pass);
    }

    public function send($to, $subject, $body)
    {
        $email = new Email();
        $email
            ->addTo($to)
            ->setFrom('info@favourhood.org')
            ->setSubject($subject)
            ->setHtml($body)
        ;
        $this->sg->send($email);
    }
}

