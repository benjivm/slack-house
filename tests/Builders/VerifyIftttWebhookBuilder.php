<?php

namespace Test\Builders;

use App\Middleware\VerifyIftttWebhook;
use JsonSchema\Validator;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class VerifyIftttWebhookBuilder extends TestCase
{
    private $logger = null;

    private $config = [];

    private $validator = null;

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

    public function withValidatorMock()
    {
        $this->validator = $this->getMockBuilder(Validator::class)
            ->setMethods(null)
            ->getMock();

        return $this;
    }

    public function withValidatorStub()
    {
        $this->validator = $this->getMockBuilder(Validator::class)
            ->getMock();

        return $this;
    }

    public function withValidatonPassed()
    {
        $this->validator->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        return $this;
    }

    public function build()
    {
        $container = (object) [];
        $container->logger = $this->logger;
        $container->config = $this->config;
        $container->validator = $this->validator;

        return new VerifyIftttWebhook($container);
    }
}
