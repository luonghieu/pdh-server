<?php

namespace App\Observers;

use App\Message;

class MessageObserver
{
    public function created(Message $message)
    {
        // TODO update read_at for sender
    }
}
