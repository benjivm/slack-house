<?php

namespace Test\Builders;

use App\Middleware\VerifyPlexWebhook;
use PHPUnit\Framework\TestCase;
use Monolog\Logger;

class VerifyPlexWebhookBuilder extends TestCase
{
    public function withMonologStub()
    {
        $this->logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $this;
    }

    public function withConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    public function build()
    {
        $container = (object)[];
        $container->logger = $this->logger;
        $container->config = $this->config;

        return new VerifyPlexWebhook($container);
    }
}
