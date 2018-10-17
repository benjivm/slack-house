<?php

namespace Test\Builders;

use Monolog\Logger;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use App\Middleware\VerifyPlexWebhook;

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
        return new VerifyPlexWebhook($this->logger, $this->config, $this->validator);
    }
}
