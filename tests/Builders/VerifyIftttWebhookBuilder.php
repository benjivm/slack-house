<?php

namespace Test\Builders;

use Monolog\Logger;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use App\Middleware\VerifyIftttWebhook;

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

    public function withValidationPassed()
    {
        $this->validator->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        return $this;
    }

    public function build()
    {
        return new VerifyIftttWebhook($this->logger, $this->config, $this->validator);
    }
}
