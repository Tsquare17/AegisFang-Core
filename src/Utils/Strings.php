<?php

namespace AegisFang\Utils;

class Strings
{
    /**
     * Get the singular form.
     *
     * @param string $string
     *
     * @return string
     */
    public static function getSingular(string $string): string
    {
        if (strpos(strrev($string), 'sei') === 0) {
            return substr($string, 0, -3) . 'y';
        }

        if (strpos(strrev($string), 's') === 0) {
            return substr($string, 0, -1);
        }

        return $string;
    }

    /**
     * Convert snake case to pascal case.
     *
     * @param string $name
     *
     * @return string
     */
    public static function snakeToPascal(string $name): string
    {
        return str_replace('_', '', ucwords($name, '_'));
    }
}
