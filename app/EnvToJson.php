<?php

namespace App;

class EnvToJson
{
    public $input;

    public $output;

    /**
     * EnvToJson constructor.
     *
     * @param $input
     * @param $output
     */
    public function __construct($input, $output)
    {
        $this->input = $input;

        $this->output = $output;
    }

    public function generate()
    {
        $env = parse_ini_file($this->input);

        $config = [];

        foreach ($env as $key => $value) {
            $setting = explode('_', $key);
            $parentKey = ucfirst(strtolower($setting[0]));

            // Remove the parent key so we're left with the children
            unset($setting[0]);

            if (strpos($key, '_') === false) {
                $config[$parentKey] = $value;
            } else {
                // Convert SETTINGS_LIKE_THIS to PascalCase
                $setting = str_replace('_', '', ucwords(strtolower(implode('_', $setting)), '_'));

                if (strpos($value, ',') === false && strpos($value, ':') !== false) {
                    $item = explode(':', $value);
                    $value = [
                        $item[0] => $item[1],
                    ];
                } elseif (strpos($value, ',') !== false && strpos($value, ':') !== false) {
                    $value = explode(',', $value);

                    foreach ($value as $key => $item) {
                        unset($value[$key]);
                        $item = explode(':', $item);
                        $value[$item[0]] = $item[1];
                    }
                } elseif (strpos($value, ',') !== false) {
                    $value = explode(',', $value);
                }

                if (array_key_exists($parentKey, $config)) {
                    $config[$parentKey][$setting] = $value;
                } else {
                    $config[$parentKey] = [
                        $setting => $value,
                    ];
                }
            }
        }

        file_put_contents($this->output, json_encode($config, JSON_PRETTY_PRINT));
    }
}
