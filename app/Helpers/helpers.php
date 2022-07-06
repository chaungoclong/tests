<?php

if (!function_exists('flatArray')) {
    /**
     * Flatten array
     *
     * @param array $array
     *
     * @return array
     */
    function flatArray(array $array): array
    {
        $result = [];

        array_walk_recursive($array, function ($a) use (&$result) {
            $result[] = $a;
        });

        return $result;
    }
}
