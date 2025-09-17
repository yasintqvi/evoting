<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\Response;

class UserHasBeenBlockedException extends Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $message = __('auth.user_has_been_blocked');

        return parent::__construct($message, Response::HTTP_UNAUTHORIZED);
    }
}
