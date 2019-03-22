<?php

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return realpath(__DIR__.'/../../').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
