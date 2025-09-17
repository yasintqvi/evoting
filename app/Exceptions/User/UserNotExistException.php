<?php

namespace App\Exceptions\User;

use Exception;
use Illuminate\Http\Response;

class UserNotExistException extends Exception
{
    public function __construct()
    {
        $message = __('auth.user_not_exist');

        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }
}
