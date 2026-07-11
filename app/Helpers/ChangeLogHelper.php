<?php

namespace App\Helpers;

class ChangeLogHelper
{
    /**
     * Mask the middle characters of a string.
     */
    public static function maskMiddle(?string $string, string $placeholder = '*'): string
    {
        if (empty($string)) {
            return '';
        }
        $len = strlen($string);
        if ($len <= 2) {
            return str_repeat($placeholder, $len);
        }
        return $string[0] . str_repeat($placeholder, $len - 2) . $string[$len - 1];
    }

    /**
     * Mask an email address mailbox portion while leaving the domain intact.
     */
    public static function maskEmail(?string $email): string
    {
        if (empty($email)) {
            return '';
        }
        $parts = explode('@', $email);
        if (count($parts) < 2) {
            return self::maskMiddle($email);
        }
        $local = $parts[0];
        $domain = $parts[1];
        return self::maskMiddle($local) . '@' . $domain;
    }
}
