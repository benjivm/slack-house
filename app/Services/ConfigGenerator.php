<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;

class ConfigGenerator
{
    public $envFile;

    public $outputFilename;

    public $options;

    private $config;

    private $envData;

    private $settingCounts;

    public function __construct($envFile, $outputFilename, $options = [], $config = [], $envData = [], $settingCounts = [])
    {
        $this->envFile = $envFile;
        $this->outputFilename = $outputFilename;
        $this->options = $options;
        $this->config = $config;
        $this->envData = $envData;
        $this->settingCounts = $settingCounts;
    }

    public function generate()
    {
        $this->loadData();

        foreach ($this->envData as $key => $value) {
            $this->iterate($key, $value);
        }

        return $this;
    }

    private function loadData()
    {
        $this->envData = parse_ini_file($this->envFile);

        if (in_array('singleKeys', $this->options)) {
            $this->getSettingCounts($this->envData);
        }
    }

    private function processKeys($setting)
    {
        $settingName = explode('_', $setting);

        if (in_array('singleKeys', $this->options)) {
            if ($this->settingCounts[$settingName[0]] === 1) {
                $parentKey = $this->applyNamingConvention($settingName);
            } else {
                $parentKey = strtolower($settingName[0]);
                unset($settingName[0]);
            }
        } else {
            $parentKey = strtolower($settingName[0]);
            unset($settingName[0]);
        }

        return [
            'settingName' => $settingName,
            'parentKey' => $parentKey,
        ];
    }

    // Returns the value in either snake_case or camelCase.
    private function applyNamingConvention($settingName, $delimiter = '_')
    {
        if (is_array($settingName)) {
            $settingName = implode($delimiter, $settingName);
        }

        if (in_array('snakeCase', $this->options)) {
            return strtolower($settingName);
        }

        return str_replace($delimiter, '', lcfirst(ucwords(strtolower($settingName), $delimiter)));
    }

    private function iterate($key, $value)
    {
        $settingName = $this->processKeys($key)['settingName'];
        $parentKey = $this->processKeys($key)['parentKey'];

        // Single value option
        if (strpos($key, '_') === false) {
            $settingName = $parentKey;
        }

        // Convert the setting name to the proper naming convention
        $settingName = $this->applyNamingConvention($settingName);

        // The value is a single named array item (e.g., Item::Value)
        if (strpos($value, ',') === false && strpos($value, '::') !== false) {
            $item = explode('::', $value);

            $value = [
                $item[0] => $item[1],
            ];

            return $this->setValue($parentKey, $settingName, $value);
        }

        // The value is an array of named array items (e.g., Item::Value,Foo::Bar)
        if (strpos($value, ',') !== false && strpos($value, '::') !== false) {
            $value = explode(',', $value);
            foreach ($value as $valueKey => $item) {
                unset($value[$valueKey]);

                $item = explode('::', $item);

                $value[$item[0]] = $item[1];
            }

            return $this->setValue($parentKey, $settingName, $value);
        }

        // The value is a normal array
        if (strpos($value, ',') !== false) {
            $value = explode(',', $value);

            return $this->setValue($parentKey, $settingName, $value);
        }

        // The value is a single string
        return $this->setValue($parentKey, $settingName, $value);
    }

    private function setValue($parent, $key, $value)
    {
        if ($parent === $key) {
            return $this->config[$key] = $value;
        }

        if (array_key_exists($parent, $this->config)) {
            return $this->config[$parent][$key] = $value;
        }

        return $this->config[$parent] = [$key => $value];
    }

    private function getSettingCounts($settings, $result = [])
    {
        foreach ($settings as $key => $value) {
            $item = explode('_', $key);

            if (array_key_exists($item[0], $result)) {
                $result[$item[0]] = $result[$item[0]] + 1;
            } else {
                $result[$item[0]] = 1;
            }
        }

        $this->settingCounts = $result;
    }

    /**
     * Write the file to php.
     */
    public function php()
    {
        $config = var_export($this->config, true);
        $config = preg_replace('/^([ ]*)(.*)/m', '$1$1$2', $config);
        $array = preg_split("/\r\n|\n|\r/", $config);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);
        $config = implode(PHP_EOL, array_filter(['['] + $array));

        file_put_contents($this->outputFilename, "<?php\n\n return {$config};");
    }

    /**
     * Write the file to json.
     */
    public function json()
    {
        file_put_contents($this->outputFilename, json_encode($this->config, JSON_PRETTY_PRINT));
    }

    /**
     * Write the file to yaml.
     */
    public function yaml()
    {
        file_put_contents($this->outputFilename, Yaml::dump($this->config));
    }
}
