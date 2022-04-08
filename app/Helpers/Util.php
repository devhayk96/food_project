<?php

namespace App\Helpers;

use App\Models\Device;
use App\Models\User;

class Util
{
    /**
     * @return string
     */
    public static function generatePinCode(): string
    {
        $length = request()->get('pin_code_length');
        $range_length = $length && in_array($length, User::PIN_CODE_LENGTHS) ? $length : User::DEFAULT_PIN_CODE_LENGTH;

        do {
            $random_pin_code = self::randomNumbersByRange($range_length);
        } while (User::where('pin_code', $random_pin_code)->exists());

        return $random_pin_code;
    }

    /**
     * @param $numbers_length
     * @return int
     */
    public static function randomNumbersByRange($numbers_length): int
    {
        $start = '1';
        $end = '9';

        for ($i = 1; $i < $numbers_length; $i++) {
            $start .= '0';
            $end .= '9';
        }

        return mt_rand($start, $end);
    }

    /**
     * @return string
     */
    public static function generateDeviceCode(): string
    {
        do {
            $random_code = self::quickRandom(4);
        } while (Device::where('code', $random_code)->exists());

        return $random_code;
    }


    public static function quickRandom($length = 16)
    {
        $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

}
