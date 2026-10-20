<?php

namespace App\Utils;

use http\Exception\InvalidArgumentException;

class StringManipulator
{
    public function toCamelCase(string $string): string
    {
        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        return lcfirst($string);
    }

    public function toSnakeCase(string $string): string
    {
        $string = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);

        if (null === $string) {
            throw new InvalidArgumentException('Invalid string provided for snake case conversion.');
        }

        return strtolower($string);
    }
}
