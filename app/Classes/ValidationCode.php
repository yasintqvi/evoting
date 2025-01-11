<?php

namespace App\Classes;

/**
 * Class ValidationCode
 */
final class ValidationCode
{
    /**
     * Generate a validation code
     *
     * @return string
     */
    public static function generate()
    {
        $length = config('auth.constants.code_length');

        return substr(str_shuffle('0123456789'), 0, $length);
    }
}
