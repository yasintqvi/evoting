<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertPersianNumbers
{
    /**
     * Input keys that must NOT be touched (digits inside them are meaningful as-is).
     *
     * @var array<int, string>
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
    ];

    /**
     * Convert any Persian/Arabic digits in the request input to English digits
     * so values are always stored in the database using Latin numerals.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value) && !in_array($key, $this->except, true)) {
                $value = convert_persian_to_english($value);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
