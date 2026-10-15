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
        $length = (int) config('auth.constants.code_length', 5);

        return implode('', array_map(fn () => (string) random_int(0, 9), range(1, $length)));
    }
}
