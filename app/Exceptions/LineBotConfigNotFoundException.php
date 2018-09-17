<?php

namespace App\Exceptions;

class LineBotConfigNotFoundException extends \Exception
{

    public function __construct($message)
    {
        parent::__construct($message);
    }

}