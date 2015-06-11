<?php

namespace AppBundle\User;

class UserAlreadyExistsException extends \RuntimeException
{
    public function __construct($username, \Exception $e = null)
    {
        $message = sprintf(
            'The username "%s" already exists.',
            $username
        );

        parent::__construct($message, 0, $e);
    }
}
