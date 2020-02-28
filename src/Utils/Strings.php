<?php

namespace AegisFang\Utils;

/**
 * Class Strings
 * @package AegisFang\Utils
 */
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
     * @param string $string
     *
     * @return string
     */
    public static function snakeToPascal(string $string): string
    {
        return str_replace('_', '', ucwords($string, '_'));
    }

    /**
     * Convert pascal case to snake case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function pascalToSnake(string $string): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);

        foreach ($matches[0] as &$match) {
            $match = ($match === strtoupper($match)) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $matches[0]);
    }
}
