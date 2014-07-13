<?php

namespace Bh\Bundle\Services;

use Services_Twilio;

class PhoneService
{
    private $twilio;
    private $number;
    public function __construct($id, $token, $number)
    {
        $this->twilio = new Services_Twilio($id, $token);
        $this->number = $number;
    }
}

