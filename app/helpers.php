<?php

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return realpath(__DIR__ . '/../') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('change_env')) {
    /**
     * Change an .env file setting.
     *
     * @param $key
     * @param $value
     *
     * @return string
     */
    function change_env($key, $value)
    {
        $path = base_path('.env');

        $oldValue = getenv($key);

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                "$key=" . $oldValue, "$key=" . $value, file_get_contents($path)
            ));
        }
    }
}
