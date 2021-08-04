<?php


namespace App\Helpers;


class Money
{
    /**
     * Move the two decimals to integer part of number (amount * 100)
     * @param float $amount
     * @return int
     */
    public static function toInternalMoney(float $amount): int
    {
        return intval($amount * 100);
    }

    /**
     * Move the last two digits to the decimal part (amount / 100)
     * @param int $amount
     * @return float
     */
    public static function toRealMoney(int $amount): float
    {
        return $amount / 100;
    }

    /**
     * Return a money format string of internal amount integer
     * @param int $amount
     * @return string
     */
    public static function format(int $amount): string
    {
        return number_format(self::toRealMoney($amount), 2, '.', ',');
    }

}