<?php

namespace Test\App\Middleware;

use App\Middleware\VerifyIftttWebhook;
use PHPUnit\Framework\TestCase;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Builders\VerifyIftttWebhookBuilder;

class VerifyIftttWebhookTest extends TestCase
{
    public function setUp()
    {
        $this->builder = new VerifyIftttWebhookBuilder();
    }

    public function testInstanciation()
    {
        $verifyIftttWebhook = $this->builder->withConfig(['ifttt' => []])
            ->withMonologStub()
            ->withValidatorStub()
            ->build();

        $this->assertInstanceOf(VerifyIftttWebhook::class, $verifyIftttWebhook);
    }

    public function testSchemaIsInvalid()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn([]);

        $verifyIftttWebhook = $this->builder->withConfig(['ifttt' => []])
            ->withMonologStub()
            ->withValidatorMock()
            ->build();

        $response = $verifyIftttWebhook($request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testIftttKeyMakerValidation()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['key' => 'test']);

        $config = [
            'ifttt' => ['key' => 'key'],
        ];

        $verifyIftttWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorStub()
            ->withValidatonPassed()
            ->build();

        $response = $verifyIftttWebhook($request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
