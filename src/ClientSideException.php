<?php

namespace TestRuimin;

class ClientSideException extends \Exception
{
    public function errorMessage()
    {
        return 'Client Side Error';
    }
}
