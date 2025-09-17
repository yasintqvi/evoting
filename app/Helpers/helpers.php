<?php

use App\Classes\V1\SettingHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;



if (!function_exists('convert_persian_to_english')) {
    function convert_persian_to_english($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $string = str_replace($persian, $english, $string);
        $string = str_replace($arabic, $english, $string);

        return $string;
    }
}

if (! function_exists('user')) {
    function user(): ?User
    {
        if (! Auth::check()) {
            return null;
        }

        return Auth::user();
    }
}

if (! function_exists('ed')) {
    function ed($string)
    {
        $persian_and_arabic_chars = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٤', '٥', '٦', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $latin_chars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '4', '5', '6', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($persian_and_arabic_chars, $latin_chars, $string);
    }
}

if (! function_exists('is_json')) {
    /**
     * @param  string  $string
     * @return bool
     */
    function is_json($string)
    {
        if (is_array($string)) {
            return false;
        }
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}

if (! function_exists('is_not_null')) {
    /**
     * @return bool
     */
    function is_not_null($var)
    {
        return ! is_null($var);
    }
}

if (! function_exists('format_phone')) {
    /**
     * Format phone
     *
     * @return array|string
     */
    function format_phone(string $phone)
    {
        $phone = preg_replace('/^\+98/', '0', $phone);

        if (! preg_match('/^0/', $phone)) {
            $phone = '0' . $phone;
        }

        return ed($phone);
    }
}

if (!function_exists('is_active')) {
    function is_active(string $address_segment)
    {
        return str_contains(request()->uri(), $address_segment);
    }
}
