<?php

namespace App\Interfaces;

use Symfony\Component\Dotenv\Dotenv;

class AppCommandInterface
{
    /**
     * Change a value in the .env file.
     *
     * @param $key
     * @param $value
     */
    public function changeConfigSetting($key, $value)
    {
        $envFile = base_path('.env');

        $dotenv = new Dotenv();
        $dotenv->load($envFile);

        $oldValue = getenv($key);

        if (file_exists($envFile) && ! empty($oldValue)) {
            file_put_contents($envFile, str_replace(
                "$key=" . $oldValue, "$key=" . $value, file_get_contents($envFile)
            ));
        }

        $this->regenerateConfig();
    }

    /**
     * Regenerate the app config file.
     */
    public function regenerateConfig()
    {
        shell_exec('php ' . base_path('slack config:generate'));
    }
}
