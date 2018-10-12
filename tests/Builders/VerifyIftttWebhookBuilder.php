<?php

namespace Test\Builders;

use App\Middleware\VerifyIftttWebhook;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class VerifyIftttWebhookBuilder extends TestCase
{
    private $logger = null;

    private $config = [];

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
        $container = (object) [];
        $container->logger = $this->logger;
        $container->config = $this->config;

        return new VerifyIftttWebhook($container);
    }
}
